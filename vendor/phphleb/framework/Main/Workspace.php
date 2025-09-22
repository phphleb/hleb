<?php

/*declare(strict_types=1);*/

namespace Hleb\Main;

use App\Bootstrap\Events\ControllerEvent;
use App\Bootstrap\Events\MiddlewareEvent;
use App\Bootstrap\Events\ModuleEvent;
use App\Bootstrap\Events\PageEvent;
use AsyncExitException;
use Hleb\Base\Event;
use Hleb\Base\PageController;
use Hleb\Constructor\Data\DebugAnalytics;
use Hleb\Constructor\Data\DynamicParams;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\Constructor\Data\View;
use Hleb\AsyncRouteException;
use Hleb\Constructor\DI\DependencyInjection;
use Hleb\CoreProcessException;
use Hleb\DynamicStateException;
use Hleb\HttpException;
use Hleb\HttpMethods\External\Response as SystemResponse;
use Hleb\ParseException;
use Hleb\Reference\ResponseInterface;
use Hleb\RouteColoredException;
use Hleb\Helpers\ReflectionMethod;
use Hleb\Static\Redirect;
use Hleb\Static\Response;
use Phphleb\Adminpan\Src\ViewPage;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * @internal
 */
final class Workspace
{
    /**
     * A pattern for replacing a controller or method
     * with the current request method.
     *
     * Паттерн для подмены в контроллере или методе
     * на текущий метод запроса.
     */
    private const PATTERN = '[verb]';

    private array $pageData = [];

    private bool $isDebug = false;

    private bool $setCoreTime = false;

    /**
     * @throws AsyncExitException|RouteColoredException|HttpException
     */
    public function extract(array $block): bool
    {
        $data = $block['data'];
        $view = $data['view'];
        $params = DynamicParams::getDynamicUriParams();

        $this->isDebug = DynamicParams::isDebug();

        if (!empty($block['middlewares']) && $this->middlewareUsage($block['middlewares'], false) === false) {
            async_exit();
        }

        $after = $block['middleware-after'] ?? [];

        if (\is_string($view)) {
            ProjectLoader::renderSimpleValue($view, $block['full-address']);
            return !($after && $this->middlewareUsage($after) === false);
        }
        if ($view !== null) {
            $this->renderView($view['template'], $view['params'], $view['status']);
            $after and $this->middlewareUsage($after);
            return true;
        }
        if (isset($block['controller'])) {
            $result = $this->controllerHandler($this->controllerUsage($block, $params, ControllerEvent::class, null));
            $after and $this->middlewareUsage($after);
            return $result;
        }
        if (isset($block['module'])) {
            $result = $this->controllerHandler($this->moduleUsage($block, $params));
            $after and $this->middlewareUsage($after);
            return $result;
        }
        if (isset($block['page'])) {
            $result = $this->pageHandler($block, $params);
            $after and $this->middlewareUsage($after);
            return $result;
        }
        if (isset($block['redirect'])) {
            $this->redirectHandler($block, $params);
        }
        return false;
    }

    /**
     * @throws AsyncExitException
     */
    private function redirectHandler(array $block, array $params): void
    {
        $location = $block['redirect']['location'];
        foreach($params as $key => $param) {
            $location = \str_replace("{%{$key}%}", (string)$param, $location);
        }

        Redirect::to($location, $block['redirect']['status']);
    }

    /**
     * @throws RouteColoredException|HttpException|AsyncExitException
     */
    private function pageHandler(array $block, array $params): bool
    {
        $name = $block['page']['name'];
        $class = $block['page']['class'];
        $allowed = SystemSettings::getValue('system', 'allowed.structure.parts');
        if ($allowed && !in_array($name, $allowed)) {
            throw new CoreProcessException("This panel type ($name) is not allowed in the configuration.");
        }
        $relPath = "@/config/structure/$name.php";
        $path = SystemSettings::getRealPath($relPath);
        if (!$path || !SystemSettings::getRealPath('@library/adminpan')) {
            $this->error(AsyncRouteException::HL31_ERROR, ['name' => $name, 'path' => "@/config/structure/$name.php"]);
        }
        if (!is_subclass_of($class, PageController::class)) {
            throw new CoreProcessException("The controller `$class` must be inherited from " . PageController::class);
        }
        $contentResult = $this->controllerHandler($this->controllerUsage($block, $params, PageEvent::class, $name));
        if ($contentResult === false) {
            async_exit();
        }
        try {
            $headerContent = ViewPage::viewHeader($this->pageData, $name);
        } catch (HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CoreProcessException($e);
        }
        $content = Response::getBody();
        $footerContent = ViewPage::viewFooter();
        Response::setBody($headerContent . $content . $footerContent);

        return $contentResult;
    }

    /**
     * @throws RouteColoredException|AsyncExitException
     */
    private function middlewareUsage(array $list, bool $after = true): bool
    {
        foreach ($list as $middleware) {
            $initiator = $middleware['class'];
            $method = $middleware['class-method'];
            if (!$this->checkMethod($initiator, $method)) {
                continue;
            }
            DynamicParams::setControllerRelatedData($middleware['related-data']);
            $refConstruct = new ReflectionMethod($initiator, '__construct');
            if (\class_exists($initiator) && \is_callable([$initiatorObject = new $initiator(
                    ...($refConstruct->countArgs() > 1 ? DependencyInjection::prepare($refConstruct) : [])
                ), $method])) {

                $refMethod = new ReflectionMethod($initiator, $method);
                $arguments = $refMethod->countArgs() ? DependencyInjection::prepare($refMethod) : [];

                $event = $this->getEventIfExists(MiddlewareEvent::class);
                if ($event) {
                    $this->updateCoreTime();
                    // The event is generated and the constructor method is executed using DI.
                    // Производится формирование события и выполнение метода конструктора посредством DI.
                    $arguments = $event->before($initiator, $method, $arguments, $after);
                    $this->updateDebug($event::class);
                }

                if ($arguments === false) {
                    async_exit();
                }

                // Execute the middleware class method.
                // Выполнение метода контроллера-посредника.
                $result = $initiatorObject->{$method}(...$arguments);

                $this->updateDebug($initiator);

                // Execute the subsequent event.
                // Выполнение последующего события.
                $continued = !$event || $this->updateMiddlewareAfterEvent($initiatorObject::class, $method, $event, $after, $result);

                if ($result === false) {
                    async_exit();
                }
                if (\is_array($result)) {
                    try {
                        $this->renderValue(\json_encode($result, JSON_THROW_ON_ERROR));
                        return false;
                    } catch (\JsonException $e) {
                        throw new ParseException($e);
                    }
                }
                if ($result !== null) {
                    $this->renderValue((string)$result);
                }
                DynamicParams::setControllerRelatedData([]);

                if ($continued === false) {
                    async_exit();
                }
            } else {
                $this->error(AsyncRouteException::HL21_ERROR, ['class' => $initiator, 'method' => $method]);
            }
        }
        return true;
    }

    /**
     * @throws RouteColoredException|AsyncExitException
     */
    private function controllerUsage(array $block, array $params, string $event, ?string $type): View|SystemResponse|ResponseInterface|PsrResponseInterface|array|string|bool|int|float
    {
        $controller = $block['controller'] ?? $block['page'];
        return $this->baseController($controller['class'], $controller['class-method'], $params, $event, $type);
    }

    /**
     * @throws RouteColoredException|AsyncExitException
     */
    private function moduleUsage(array $block, array $params): View|SystemResponse|ResponseInterface|PsrResponseInterface|array|string|bool|int|float
    {
        $module = $block['module'];
        $name = $module['name'];
        DynamicParams::setActiveModuleName($name);

        return $this->baseController($module['class'], $module['class-method'], $params, ModuleEvent::class, $name);
    }

    /**
     * Outputting a simple value.
     *
     * Вывод простого значения.
     */
    private function renderValue(mixed $value): true
    {
        Response::addToBody($value);

        return true;
    }


    /**
     * Template output.
     *
     * Вывод шаблона.
     */
    private function renderView(string $template, array $params = [], ?int $status = null): true
    {
        if (\in_array($template, ['403', '404', '401'])) {
            (new ErrorTemplates($template))->searchAndThrowError();
        }
        $status = $status ?: Response::getStatus();
        Response::setStatus($status);
        Response::addToBody(\template($template, $params));

        return true;
    }

    /**
     * Processing data received from the controller.
     *
     * Обработка данных, полученных из контроллера.
     */
    private function controllerHandler(View|SystemResponse|ResponseInterface|PsrResponseInterface|array|string|bool|int|float $data): bool
    {
        if ($data instanceof View) {
            $view = $data->toArray();
            if (\in_array($view['template'], ['403', '404', '401'])) {
                (new ErrorTemplates($view['template']))->searchAndThrowError();
            }
            return $this->renderView($view['template'], $view['params'], $view['status']);
        }
        if (\is_bool($data)) {
            return $data;
        }
        if (\is_array($data)) {
            try {
                return $this->renderValue(\json_encode($data, JSON_THROW_ON_ERROR));
            } catch (\JsonException $e) {
                throw new ParseException($e);
            }
        }
        if (\is_object($data)) {
            if ($data instanceof SystemResponse) {
                Response::init($data);
                return true;
            }
            if ($data instanceof ResponseInterface) {
                Response::init($data->getInstance());
                return true;
            }
            if (\is_a($data, PsrResponseInterface::class)) {
                /** @var PsrResponseInterface $data */
                Response::init(new SystemResponse($data->getBody(), $data->getStatusCode(), $data->getHeaders()));
                return true;
            }
        }

        return $this->renderValue($data);
    }

    /**
     * @throws RouteColoredException|AsyncExitException
     */
    private function baseController(
        string  $className,
        string  $method,
        array   $params,
        string  $eventClass,
        ?string $type = null,
    ): View|SystemResponse|ResponseInterface|PsrResponseInterface|array|string|bool|int|float
    {
        // If tags are inserted into the name of the class or method.
        // Если в название класса или метода вставлены теги.
        $countTags = \substr_count($className . $method, '<');
        if ($countTags > 0) {
            $extractor = new RouteExtractor();
            $params = DynamicParams::getDynamicUriParams();
            [$className, $method] = $extractor->getCalledClassAndMethod($className, $method, $countTags, $params);
        }
        if (\str_contains($className, self::PATTERN) || \str_contains($method, self::PATTERN)) {
            isset($extractor) or $extractor = new RouteExtractor();
            $insert = DynamicParams::getRequest()->getMethod();
            [$className, $method] = $extractor->replacePattern($className, $method, $insert);
        }
        if (!$this->checkMethod($className, $method)) {
            return false;
        }
        $initiator = $className;
        if (\class_exists($initiator)) {
            DynamicParams::setControllerMethodName($method);
            $refConstruct = new ReflectionMethod($className, '__construct');
            $initiatorObject = new $initiator(
                ...($refConstruct->countArgs() > 1 ? DependencyInjection::prepare($refConstruct) : [])
            );

            if (\is_callable([$initiatorObject, $method])) {
                $this->isDebug and DebugAnalytics::addData(DebugAnalytics::INITIATOR, "$initiator->$method()");
                $refMethod = new ReflectionMethod($initiatorObject::class, $method);
                $countArg = $refMethod->countArgs();

                $event = $this->getEventIfExists($eventClass);

                if (!$params && !$countArg) {
                    $arguments = $refMethod->countArgs() ? DependencyInjection::prepare($refMethod) : [];
                    if ($event) {
                        $this->updateCoreTime();
                        if ($type) {
                            $arguments = $event->before($initiator, $method, $arguments, $type);
                        } else {
                            $arguments = $event->before($initiator, $method, $arguments);
                        }
                        $this->updateDebug($eventClass);
                        if ($arguments === false) {
                            async_exit();
                        }
                    }

                    $result = $initiatorObject->{$method}(...$arguments) ?? true;
                    $this->updatePageData($initiatorObject);

                    $this->updateDebug($initiator);

                    $event and $this->updateControllerAfterEvent($initiatorObject::class, $method, $event, $type, $result);

                    return $this->checkControllerResultType($initiator, $method, $result);
                }
                $data = [];
                if ($params) {
                    try {
                        $errors = $refMethod->getErrorInArguments($params, favorites: \array_keys($params));
                        if ($errors) {
                            $this->error(AsyncRouteException::HL25_ERROR, [
                                'class' => $initiator,
                                'method' => $method,
                                'cells' => \implode(', ', $errors)
                            ]);
                        }
                        $data = $refMethod->convertArguments($params, favorites: \array_keys($params));
                    } catch (DynamicStateException) {
                        $this->error(AsyncRouteException::HL26_ERROR, ['class' => $initiator, 'method' => $method]);
                    }
                    if ($data === false) {
                        throw new CoreProcessException(
                            "The dynamic route parameters do not match the incoming data in method $method() of class $initiator."
                        );
                    }
                }
                // The event is generated and the constructor method is executed using DI.
                // Производится формирование события и выполнение метода конструктора посредством DI.
                $arguments = \array_merge($data, $refMethod->countArgs() ? DependencyInjection::prepare($refMethod) : []);
                if ($event) {
                    $this->updateCoreTime();
                    if ($type) {
                        $arguments = $event->before($initiator, $method, $arguments, $type);
                    } else {
                        $arguments = $event->before($initiator, $method, $arguments);
                    }
                    $this->updateDebug($eventClass);
                    if ($arguments === false) {
                        async_exit();
                    }
                }

                $result = ($initiatorObject->{$method}(...$arguments)) ?? true;
                $this->updatePageData($initiatorObject);

                $this->updateDebug($initiator);

                $event and $this->updateControllerAfterEvent($initiatorObject::class, $method, $event, $type, $result);

                if(\is_null($result)) {
                    $result = '';
                }

                return $this->checkControllerResultType($initiator, $method, $result);
            }
        }
        if ($countTags > 0) {
            return false;
        }

        $this->error(AsyncRouteException::HL21_ERROR, ['class' => $initiator, 'method' => $method]);
    }

    /**
     * @throws RouteColoredException
     */
    private function error(string $tag, array $replace = []): void
    {
        throw (new RouteColoredException($tag))->complete($this->isDebug, $replace);
    }

    /**
     * Getting prepared data after executing a controller method.
     *
     * Получение подготовленных данных после выполнения метода контроллера.
     */
    private function updatePageData(object $initiator): void
    {
        if (!\is_subclass_of($initiator, PageController::class)) {
            return;
        }
        if (\method_exists($initiator, 'getHeadData')) {
            if (!$initiator->getExternalAccess()) {
                $class = $initiator::class;
                throw new CoreProcessException("Disabled `page.external.access` to the administrative panel for the {$class} class.");
            }
            $this->pageData = $initiator->getHeadData();
        }
    }

    /**
     * Some methods should not be called from outside.
     *
     * Некоторые методы не должны быть вызваны извне.
     */
    private function checkMethod(string $class, string $method): bool
    {
        if (\str_starts_with($method, '_')) {
            throw new CoreProcessException("The $method method cannot be called on the $class class.");
        }
        // The beforeAction method is reserved for future versions of the framework.
        // Метод beforeAction зарезервирован для следующих версий фреймворка.
        if ($method === 'beforeAction') {
            return false;
        }
        return true;
    }

    /**
     * Changes the output result of the action when a subsequent controller event is executed.
     *
     * Изменяет результат вывода действия при выполнении последующего события контроллера.
     */
    private function updateControllerAfterEvent(string $class, string $method, object $event, mixed $type, mixed &$result): void
    {
        if (\method_exists($event, 'after')) {
            if ($type) {
                $event->after($class, $method, $type, $result);
            } else {
                $event->after($class, $method, $result);
            }
            $this->updateDebug($event::class . '->after');
        }
    }

    /**
     * Changes the output result of the action when a subsequent middleware event is executed.
     * Also determines whether execution will continue.
     *
     * Изменяет результат вывода действия при выполнении последующего события middleware.
     * Также определяет, будет ли продолжение выполнения.
     */
    private function updateMiddlewareAfterEvent(string $class, string $method, object $event, bool $after, mixed &$result): bool
    {
        if (\method_exists($event, 'after')) {
            $continued = $event->after($class, $method, $after, $result);
            $this->updateDebug($event::class . '->after');
            return $continued;
        }
        return true;
    }

    /**
     * Saving data for the debug panel.
     *
     * Сохранение данных для панели отладки.
     */
    private function updateDebug(string $initiator): void
    {
        if ($this->isDebug) {
            DebugAnalytics::addData(DebugAnalytics::MIDDLEWARE, ['name' => $initiator, 'time' => \microtime(true)]);
        }
    }

    /**
     * Makes corrections for preload completion time for measurement accuracy.
     *
     * Вносит поправки для времени завершения предварительной загрузки для точности измерений.
     */
    private function updateCoreTime(): void
    {
        if (!$this->setCoreTime) {
            DynamicParams::setCoreEndTime(\microtime(true));
            $this->setCoreTime = true;
        }
    }

    /**
     * Returns an event object for the class if its methods were used.
     *
     * Возвращает объект события по классу, если методы его были использованы.
     */
    private function getEventIfExists(string $eventClass): ?Event
    {
        if (SystemSettings::getValue('system', 'events.used') === false) {
            return null;
        }
        $eventMethod = new ReflectionMethod($eventClass, '__construct');
        if ($eventMethod->countArgs() > 1) {
            $event = new $eventClass(...DependencyInjection::prepare($eventMethod));
        } else if (\method_exists($eventClass, 'before') || \method_exists($eventClass, 'after')) {
            $event = new $eventClass();
        }
        return $event ?? null;
    }

    /**
     * Checking the correctness of the return type in the controller and generating a detailed error.
     *
     * Проверка правильности возвращаемого типа в контроллере с формированием подробной ошибки.
     *
     * @throws RouteColoredException
     */
    private function checkControllerResultType(string $controller, string $method, mixed $result): mixed
    {
        if (
            $result instanceof View ||
            \is_array($result) ||
            \is_string($result) ||
            \is_bool($result) ||
            \is_int($result) ||
            \is_float($result) ||
            $result instanceof SystemResponse ||
            $result instanceof ResponseInterface ||
            $result instanceof PsrResponseInterface
        ) {
            return $result;
        }
        $types = \implode(', ', [
            'array', 'int', 'string', 'bool', 'float',
            View::class,
            SystemResponse::class,
            ResponseInterface::class,
            PsrResponseInterface::class,
        ]);

        $this->error(AsyncRouteException::HL37_ERROR, ['class' => $controller, 'method' => $method, 'types' => $types]);
    }

}
