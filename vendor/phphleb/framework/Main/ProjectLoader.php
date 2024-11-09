<?php

/*declare(strict_types=1);*/

namespace Hleb\Main;

use AsyncExitException;
use Hleb\Constructor\Data\DynamicParams;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\CoreProcessException;
use Hleb\Helpers\RouteHelper;
use Hleb\HttpException;
use Hleb\HttpMethods\Intelligence\Cookies\AsyncCookies;
use \Hleb\Static\Csrf;
use Hleb\HttpMethods\Intelligence\AsyncConsolidator;
use Hleb\HttpMethods\Intelligence\Cookies\StandardCookies;
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
    /**
     * Performs all stages of the project in turn. In this case, the input / output data is initialized.
     *
     * Выполняет поочередно все стадии проекта. При этом данные ввода/вывода проинициализированы.
     *
     * @throws AsyncExitException|HttpException
     */
    public function init(): void
    {
        /** @see hl_check() - ProjectLoader start */
        if ($this->insertServiceResource()) {
            return;
        }
        /** @see hl_check() - insertServiceResource worked */

        $routes = new RouteFileManager();
        /** @see hl_check() - RouteFileManager created */

        $block = $routes->getBlock();
        /** @see hl_check() - search for a suitable block is completed */

        if ($this->searchHeadMethod()) {
            return;
        }
        /** @see hl_check() - searchHeadMethod completed */

        if ($block) {
            if ($this->searchDefaultHttpOptionsMethod($block)) {
                return;
            }
            /** @see hl_check() - search for default method is completed */

            $this->updateDataIfModule($block);
            /** @see hl_check() - updateDataIfModule completed */

            if ($this->initBlock($block, $routes)){
                return;
            }
        }

        if ($routes->isBlocked()) {
            (new BaseErrorPage(403, 'Locked resource'))->insertInResponse();
            return;
        }

        $block = $routes->getFallbackBlock();
        if ($block && $this->initBlock($block, $routes)) {
            return;
        }
        // If the block is not found, then the error page is determined.
        // Если блок не найден, то определяется страница ошибки.
        (new BaseErrorPage(404, 'Not Found'))->insertInResponse();
        $this->addDebugPanelToResponse();
    }

    /**
     * Apply settings when a module is detected.
     *
     * Применение настроек в случае обнаружения модуля.
     */
    private function updateDataIfModule(array $block): void
    {
        if (isset($block['module'])) {
            $moduleName = $block['module']['name'];
            $mainFile = SystemSettings::getRealPath("@modules/$moduleName/config/main.php");
            if ($mainFile) {
                $main = (static function () use ($mainFile): array {
                    return require $mainFile;
                })();
                SystemSettings::updateMainSettings($main);
            }
            SystemSettings::addModuleType((bool)SystemSettings::getRealPath("@modules/$moduleName/views"));

            $dbFile = SystemSettings::getRealPath("@modules/$moduleName/config/database.php");
            if ($dbFile) {
                $database = (static function () use ($dbFile): array {
                    return require $dbFile;
                })();
                SystemSettings::updateDatabaseSettings($database);
            }
        }
    }

    /**
     * Adding a debug panel.
     *
     * Добавление панели отладки.
     */
    private function addDebugPanelToResponse(): void
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
    private function addRegisterBlockIfExists(): void
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
    private function insertServiceResource(): bool
    {
        $address = DynamicParams::getRequest()->getUri()->getPath();
        if (\str_starts_with($address, '/hl') && !\str_contains($address, '.')) {
            $this->initCookies(true);
            $this->initSession(true);
            return (new LibraryServiceResources())->place();
        }
        return false;
    }

    /**
     * Sets Cookies if none are present.
     *
     * Устанавливает Cookies при их отсутствии.
     */
    private function initCookies(bool|null $disabledInRoute): void
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
    private function initSession(bool|null $disabledInRoute): void
    {
        if ($disabledInRoute === true) {
            return;
        }
        $session = DynamicParams::getAlternateSession();
        if (\is_array($session)) {
            $this->updateSession($session);
            DynamicParams::setAlternateSession($session);
            $_SESSION = $session;
            return;
        }

        if ($disabledInRoute === false || SystemSettings::getValue('main', 'session.enabled')) {
            if (SystemSettings::isStandardMode()) {
                if (\session_status() !== PHP_SESSION_ACTIVE) {
                    \session_name(SystemSettings::getSystemValue('session.name'));
                    $options = SystemSettings::getMainValue('session.options');
                    if ($options){
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
        empty($_SESSION) or $this->updateSession($_SESSION);
    }

    /**
     * Performs mandatory manipulations on sessions.
     *
     * Производит обязательные манипуляции над сессиями.
     */
    private function updateSession(array &$session): void
    {
        $id = '_hl_flash_';
        if (isset($session[$id])) {
            foreach ($session[$id] as $key => &$data) {
                $data['reps_left'] --;
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
    private function searchHeadMethod(): bool
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
    private function searchDefaultHttpOptionsMethod(array $block): bool
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
     * Returns the result of block initialization.
     *
     * Возвращает результат инициализации блока.
     *
     * @throws AsyncExitException|HttpException
     */
    private function initBlock(array $block, RouteFileManager $routes): bool
    {
        $this->initCookies($routes->getIsPlain());
        $this->initSession($routes->getIsPlain());

        DynamicParams::setDynamicUriParams($routes->getData());
        DynamicParams::setRouteName($routes->getRouteName());
        DynamicParams::setRouteClassName($routes->getRouteClassName());

        if (($protected = $routes->protected()) && \in_array('CSRF', $protected, true) && !Csrf::validate(Csrf::discover())) {
            (new BaseErrorPage(401, 'Protected from CSRF'))->insertInResponse();
            return true;
        }
        $workspace = new Workspace();
        DynamicParams::setCoreEndTime(\microtime(true));
        $result = $workspace->extract($block);
        if ($result) {
            $this->addRegisterBlockIfExists();
            DynamicParams::setEndTime(\microtime(true));
            $this->addDebugPanelToResponse();
            return true;
        }
        DynamicParams::setEndTime(\microtime(true));

        return false;
    }
}
