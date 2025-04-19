<?php

/*declare(strict_types=1);*/

namespace Hleb\Main;

use App\Bootstrap\Events\KernelEvent;
use AsyncExitException;
use Hleb\Constructor\Data\DynamicParams;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\Constructor\DI\DependencyInjection;
use Hleb\CoreProcessException;
use Hleb\Helpers\ReflectionMethod;
use Hleb\Helpers\RouteHelper;
use Hleb\HttpException;
use Hleb\HttpMethods\External\SystemRequest;
use Hleb\HttpMethods\Intelligence\Cookies\AsyncCookies;
use Hleb\Main\Routes\Search\RouteAsyncFileManager;
use \Hleb\Static\Csrf;
use Hleb\HttpMethods\Intelligence\AsyncConsolidator;
use Hleb\HttpMethods\Intelligence\Cookies\StandardCookies;
use Hleb\Static\Log;
use Hleb\Static\Request;
use Hleb\Static\Response;
use Hleb\Main\Routes\Search\RouteFileManager;
use Hleb\Main\System\LibraryServiceResources;
use Phphleb\Debugpan\InitPanel;
use Phphleb\Hlogin\App\Content\ScriptLoader;
use App\Middlewares\Hlogin\Registrar;

/**
 * Project loader.
 *
 * Загрузчик проекта.
 *
 * @internal
 */
final class ProjectLoader
{
    private static array $cachePlainRoutes = [];

    private static ?bool $kernelEventExists = null;

    /**
     * Performs all stages of the project in turn. In this case, the input / output data is initialized.
     *
     * Выполняет поочередно все стадии проекта. При этом данные ввода/вывода проинициализированы.
     *
     * @throws AsyncExitException|HttpException
     */
    public static function init(): void
    {
        /** @see hl_check() - ProjectLoader start */
        if (self::cachePlainRoutes() || self::insertServiceResource()) {
            return;
        }
        /** @see hl_check() - insertServiceResource worked */

        $routes = SystemSettings::isAsync() ? new RouteAsyncFileManager() : new RouteFileManager();
        /** @see hl_check() - RouteFileManager created */

        $block = $routes->getBlock();
        /** @see hl_check() - search for a suitable block is completed */

        if (self::searchHeadMethod()) {
            return;
        }
        /** @see hl_check() - searchHeadMethod completed */

        self::checkIsNoDebug($routes, DynamicParams::getRequest());

        if ($block) {
            if (self::searchDefaultHttpOptionsMethod($block)) {
                return;
            }
            /** @see hl_check() - search for default method is completed */

            self::updateDataIfModule($block);
            /** @see hl_check() - updateDataIfModule completed */

            if (self::initBlock($block, $routes)) {
                return;
            }
        }

        if ($routes->isBlocked()) {
            (new BaseErrorPage(403, 'Locked resource'))->insertInResponse();
            return;
        }

        $block = $routes->getFallbackBlock();
        if ($block && self::initBlock($block, $routes)) {
            return;
        }
        unset($block, $routes);
        // If the block is not found, then the error page is determined.
        // Если блок не найден, то определяется страница ошибки.
        (new BaseErrorPage(404, 'Not Found'))->insertInResponse();
        self::addDebugPanelToResponse();
    }

    /**
     * Outputting the value from the route with the assignment of headers.
     * Returns the matched data for caching.
     *
     * Вывод значения из маршрута c назначением заголовков.
     * Возвращает совпавшие данные для кеширования.
     *
     * @internal
     */
    public static function renderSimpleValue(string $value, string $address): array
    {
        $isSimple = false;
        $contentType = 'text/plain';
        if (\str_starts_with($value, \Functions::PREVIEW_TAG)) {
            $value = \substr($value, \strlen(\Functions::PREVIEW_TAG));
            $replacements = [];
            if ($hasKeyReplacement = \str_contains($value, '{%')) {
                foreach (DynamicParams::getDynamicUriParams() as $key => $param) {
                    if ("{%$key%}" === $value) {
                        return self::createSimpleCacheData($param, $contentType);
                    }
                    $replacements["{%$key%}"] = (string)$param;
                }
            }
            $hasIp = false;
            if (\str_contains($value, '{{')) {
                $hasIp = \str_contains($value, '{{ip}}');
                if ($hasIp) {
                    $replacements['{{ip}}'] = DynamicParams::getRequest()->getUri()->getIp();
                }
                if (\str_contains($value, '{{method}}')) {
                    $replacements['{{method}}'] = DynamicParams::getRequest()->getMethod();
                }
                if (\str_contains($value, '{{route}}')) {
                    $replacements['{{route}}'] = $address;
                }
            }
            if (!$hasKeyReplacement && !$hasIp) {
                $isSimple = true;
            }
            if ($replacements) {
                $value = \strtr($value, $replacements);
            }
        } else {
            $isSimple = true;
        }
        if (\str_starts_with($value, '{') && \str_ends_with($value, '}')) {
            $contentType = 'application/json';
        }

        return self::createSimpleCacheData($value, $contentType, $isSimple);
    }

    /**
     * Transformation of final data into framework format.
     *
     * Преобразование конечных данных в формат фреймворка.
     */
    private static function createSimpleCacheData(string $value, string $contentType, bool $isSimple = true): array
    {
        Response::addToBody($value);
        Response::addHeaders(['Content-Type' => $contentType]);

        if ($isSimple && SystemSettings::isAsync()) {
            return [
                'id' => DynamicParams::addressAsString(true),
                'value' => $value,
                'type' => $contentType,
            ];
        }
        return [];
    }


    /**
     * Apply settings when a module is detected.
     * For modules, the config can be either in the module or in a separate 'modules' config.
     * The modules.php file must first be allowed in the 'custom.setting.files' setting.
     *
     * Применение настроек в случае обнаружения модуля.
     * Для модулей конфигурация может быть как в модуле, так и в отдельном конфиге 'modules'.
     * Предварительно файл modules.php должен быть разрешен в настройке 'custom.setting.files'.
     */
    private static function updateDataIfModule(array $block): void
    {
        if (isset($block['module'])) {
            $moduleName = $block['module']['name'];
            $configModule = SystemSettings::getValue('modules', $moduleName);
            if ($configModule) {
                isset($configModule['main']) and SystemSettings::updateMainSettings($configModule['main']);
                isset($configModule['database']) and SystemSettings::updateDatabaseSettings($configModule['database']);
            } else {
                $mainFile = SystemSettings::getRealPath("@modules/$moduleName/config/main.php");
                if ($mainFile) {
                    $main = (static function () use ($mainFile): array {
                        return require $mainFile;
                    })();
                    SystemSettings::updateMainSettings($main);
                }
                $dbFile = SystemSettings::getRealPath("@modules/$moduleName/config/database.php");
                if ($dbFile) {
                    $database = (static function () use ($dbFile): array {
                        return require $dbFile;
                    })();
                    SystemSettings::updateDatabaseSettings($database);
                }
            }
            SystemSettings::addModuleType((bool)SystemSettings::getRealPath("@modules/$moduleName/views"));
        }
    }

    /**
     * Adding a debug panel.
     *
     * Добавление панели отладки.
     */
    private static function addDebugPanelToResponse(): void
    {
        if (DynamicParams::isDebug() &&
            DynamicParams::getRequest()->getMethod() === 'GET' &&
            SystemSettings::getRealPath('@library/debugpan')
        ) {
            Response::addToBody((new InitPanel())->createPanel());
        }
    }

    /**
     * If the registration script was not specified on the page, it will be displayed
     * if there is a middleware controller for registration.
     *
     * Если скрипт для регистрации не был задан на странице, то он будет выведен
     * при наличии middleware-контроллера для регистрации.
     */
    private static function addRegisterBlockIfExists(): void
    {
        if (\class_exists(Registrar::class, false) && Registrar::isUsed()) {
            ScriptLoader::set();
        }
    }

    /**
     * Service call to appropriate framework library resources.
     *
     * Сервисный вызов ресурсов подходящей библиотеки фреймворка.
     *
     */
    private static function insertServiceResource(): bool
    {
        $address = DynamicParams::getRequest()->getUri()->getPath();
        if (\str_starts_with($address, '/hl') && !\str_contains($address, '.')) {
            self::initCookies(true);
            self::initSession(true);
            return (new LibraryServiceResources())->place();
        }
        return false;
    }

    /**
     * Sets Cookies if none are present.
     *
     * Устанавливает Cookies при их отсутствии.
     */
    private static function initCookies(bool|null $disabledInRoute): void
    {
        if ($disabledInRoute === true) {
            return;
        }
        $cookies = DynamicParams::getAlternateCookies();
        if (\is_array($cookies)) {
            foreach ($cookies as $name => $cookie) {
                AsyncCookies::set($name, $cookie);
            }
            $_COOKIE = $cookies;
        }
    }

    /**
     * Sets the session if it doesn't exist.
     *
     * Устанавливает сессию при её отсутствии.
     *
     */
    private static function initSession(bool|null $disabledInRoute): void
    {
        if ($disabledInRoute === true) {
            return;
        }
        $session = DynamicParams::getAlternateSession();
        if (\is_array($session)) {
            self::updateSession($session);
            DynamicParams::setAlternateSession($session);
            $_SESSION = $session;
            return;
        }

        // If $disabledInRoute is null, this means that the value is not set.
        // Если $disabledInRoute равен null, то это значит, что значение не задано.
        if ($disabledInRoute === false || SystemSettings::getValue('main', 'session.enabled')) {
            if (SystemSettings::isStandardMode()) {
                if (\session_status() !== PHP_SESSION_ACTIVE) {
                    \session_name(SystemSettings::getSystemValue('session.name'));
                    $options = SystemSettings::getMainValue('session.options');
                    if ($options) {
                        \session_set_cookie_params($options);
                    } else {
                        $lifetime = SystemSettings::getSystemValue('max.session.lifetime');
                        if ($lifetime > 0) {
                            \session_set_cookie_params($lifetime);
                        }
                    }
                }
                if (!\session_id()) {
                    \session_start();
                }
                StandardCookies::sync();
            } else {
                AsyncConsolidator::initAsyncSessionAndCookies();
            }
            if (\session_status() !== PHP_SESSION_ACTIVE) {
                throw new CoreProcessException('SESSION not initialized!');
            }
        }
        empty($_SESSION) or self::updateSession($_SESSION);
    }

    /**
     * Performs mandatory manipulations on sessions.
     *
     * Производит обязательные манипуляции над сессиями.
     */
    private static function updateSession(array &$session): void
    {
        $id = '_hl_flash_';
        if (isset($session[$id])) {
            foreach ($session[$id] as $key => &$data) {
                $data['reps_left']--;
                if ($data['reps_left'] < 0) {
                    unset($session[$id][$key]);
                    continue;
                }
                if (isset($data['new'])) {
                    $data['old'] = $data['new'];
                    $data['new'] = null;
                }
                if (\is_null($data['old'])) {
                    unset($session[$id][$key]);
                }
            }
        }
    }

    /**
     * Returns the result of the search and processing of the HEAD method.
     *
     * Возвращает результат поиска и обработки метода HEAD.
     */
    private static function searchHeadMethod(): bool
    {
        if (DynamicParams::getRequest()->getMethod() === 'HEAD') {
            $allow = (new RouteHelper())->getRouteHttpMethods(
                Request::getUri()->getPath(),
                Request::getHost(),
            );
            if (\count($allow) > 2) {
                Response::setBody('');
                Response::setStatus(200);
                return true;
            }
        }
        return false;
    }

    /**
     * The standard HTTP response is the OPTIONS method,
     * unless specified separately.
     *
     * Стандартный ответ на HTTP метод OPTIONS,
     * если он не указан отдельно.
     */
    private static function searchDefaultHttpOptionsMethod(array $block): bool
    {
        if ($block['name'] !== 'options' &&
            DynamicParams::getRequest()->getMethod() === 'OPTIONS'
        ) {
            $allow = (new RouteHelper())->getRouteHttpMethods(
                Request::getUri()->getPath(),
                Request::getHost(),
            );
            Response::replaceHeaders([
                'Allow' => implode(', ', $allow),
                'Content-Length' => '0',
            ]);
            Response::setBody('');
            Response::setStatus(200);
            return true;
        }
        return false;
    }

    /**
     * Most queries that return pre-specified text in a route and run asynchronously
     * can be added to the in-memory cache.
     * If they are added, then this method composes a response and returns true.
     *
     * Большинство запросов, возвращающих предварительно указанный текст в маршруте
     * и работающих в асинхронном режиме, могут быть добавлены в in-memory кэш.
     * Если они добавлены, то данный метод составляет ответ и возвращает true.
     */
    private static function cachePlainRoutes(): bool
    {
        if (self::$cachePlainRoutes) {
            if (self::searchKernelEvent()) {
                return false;
            }
            $cache = self::$cachePlainRoutes[DynamicParams::addressAsString(true)] ?? [];
            if ($cache) {
                Response::setBody($cache['value']);
                Response::addHeaders(['Content-Type' => $cache['type']]);
                return true;
            }
        }
        return false;
    }

    /**
     * Added to the cache.
     *
     * Добавляется в кеш.
     */
    private static function addToPlainCache(array $data): void
    {
        if (!$data) {
            return;
        }
        if (\count(self::$cachePlainRoutes) > 1000) {
            \array_unshift(self::$cachePlainRoutes);
        }
        $id = $data['id'];
        unset($data['id']);
        self::$cachePlainRoutes[$id] = $data;
    }

    /**
     * Returns the result of block initialization.
     *
     * Возвращает результат инициализации блока.
     *
     * @throws AsyncExitException|HttpException
     */
    private static function initBlock(array $block, RouteFileManager $routes): bool
    {
        self::initCookies($routes->getIsPlain());
        self::initSession($routes->getIsPlain());

        DynamicParams::setDynamicUriParams($routes->getData());
        DynamicParams::setRouteName($routes->getRouteName());
        DynamicParams::setRouteClassName($routes->getRouteClassName());

        if (($protected = $routes->protected()) && \in_array('CSRF', $protected, true) && !Csrf::validate($token = Csrf::discover())) {
            $message = 'Access to the protected route was prevented due to an invalid CSRF protection key';
            if (empty($token)) {
                $message = 'Access to the protected route is prevented unless the CSRF protection key is specified';
            }
            $level = SystemSettings::getCommonValue('system.log.level') ?: 'warning';
            Log::log($level, $message, ['tag' => '#security_message']);

            (new BaseErrorPage(401, 'Protected from CSRF'))->insertInResponse();
            return true;
        }
        if (self::searchKernelEvent() && self::runKernelEventAndExit()) {
            return true;
        }
        // If this is simple text, then we will process it here.
        // Если это простой текст, то обработаем его здесь.
        if (empty($block['middlewares']) && empty($block['middleware-after']) && \is_string($block['data']['view'] ?? null)) {
            self::addToPlainCache(self::renderSimpleValue($block['data']['view'], $block['full-address']));
            return true;
        }

        $workspace = new Workspace();
        DynamicParams::setCoreEndTime(\microtime(true));
        $result = $workspace->extract($block);
        if ($result) {
            self::addRegisterBlockIfExists();
            DynamicParams::setEndTime(\microtime(true));
            self::addDebugPanelToResponse();
            return true;
        }
        DynamicParams::setEndTime(\microtime(true));

        return false;
    }

    /**
     * Returns the result of checking the existence of the KernelEvent class.
     *
     * Возвращает результат проверки существования класса KernelEvent.
     *
     * @see self::runKernelEventAndExit()
     */
    private static function searchKernelEvent(): bool
    {
        if ((SystemSettings::getSystemValue('events.used') ?? true) !== false) {
            if (\is_null(self::$kernelEventExists)) {
                $file = SystemSettings::getPath('@global/app/Bootstrap/Events/KernelEvent.php');
                self::$kernelEventExists = \file_exists($file);
                if (self::$kernelEventExists) {
                    require $file;
                }
            }
        }
        return (bool)self::$kernelEventExists;
    }

    /**
     * Implements a check for the existence of the KernelEvent class and returns its execution status.
     * The KernelEvent class is used only to interfere with the pre-request logic.
     * Initially, such a file is not in the Events folder; to use it, you need to create it.
     * The class constructor, if added, supports framework dependency injection.
     * In the debug panel output, the event execution time is included in the framework initialization.
     * Example file /app/Bootstrap/Events/KernelEvent.php:
     *
     * Реализует проверку существования класса KernelEvent и возврат статуса его выполнения.
     * Класс KernelEvent используется только для вмешательства в предварительную логику запроса.
     * Первоначально такого файла нет в папке с Событиями, для использования нужно его создать.
     * Конструктор класса, если добавлен, поддерживает внедрение зависимостей фреймворка.
     * В выводе панели отладки время выполнения события входит в инициализацию фреймворка.
     * Пример файла /app/Bootstrap/Events/KernelEvent.php:
     *
     * ```php
     * <?php
     *
     * namespace App\Bootstrap\Events;
     *
     * use Hleb\Base\Event;
     *
     * final class KernelEvent extends Event
     * {
     *     public function before(): bool
     *     {
     *         // Completion of execution, if false, then exit the script immediately.
     *         return true;
     *     }
     * }
     * ```
     *
     */
    protected static function runKernelEventAndExit(): bool
    {
        if (self::$kernelEventExists) {
            if (\method_exists(KernelEvent::class, '__construct')) {
                $refConstruct = new ReflectionMethod(KernelEvent::class, '__construct');
                $event = new KernelEvent(
                    ...($refConstruct->countArgs() ? DependencyInjection::prepare($refConstruct) : [])
                );
            } else {
                $event = new KernelEvent();
            }
            return !$event->before();
        }
        return false;
    }

    /**
     * Makes adjustments to debug mode activity for certain conditions.
     *
     * Вносит поправки в активность режима отладки для определенных условий.
     */
    protected static function checkIsNoDebug(RouteFileManager $routes, SystemRequest $request): void
    {
        if ($routes->getIsNoDebug() &&
            ($request->getGetParam('_debug') ?? $request->getPostParam('_debug')) !== 'on'
        ) {
            DynamicParams::setDynamicDebug(false);
        }
    }
}
