<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Prepare;

use Hleb\Constructor\Data\DynamicParams;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\AsyncRouteException;
use Hleb\Helpers\NameConverter;
use Hleb\RouteColoredException;
use Hleb\Main\Routes\StandardRoute;
use Hleb\Static\Settings;

/**
 * @internal
 */
final readonly class FileChecker
{
    private NameConverter $converter;

    public function __construct(private array $rawRoutes)
    {
        $this->converter = new NameConverter();
    }

    /**
     * Performs an existence check for controller and template classes.
     * The check is performed only for immutable values, into which it is impossible
     * to substitute a variable through the <...> tag.
     *
     * Осуществляет проверку на существование для классов контроллеров и шаблонов.
     * Проверка выполняется только для неизменяемых значений, в которые нельзя
     * подставить переменную через тег <...>.
     *
     * @throws RouteColoredException
     */
    public function isCheckedOrError(): true
    {
        foreach ($this->rawRoutes as $route) {
            $method = $route['method'];
            if ($method === StandardRoute::CONTROLLER_TYPE) {
                $this->checkController($route);
                continue;
            }
            if ($method === StandardRoute::PAGE_TYPE) {
                $this->checkPage($route);
                continue;
            }
            if ($method === StandardRoute::MIDDLEWARE_TYPE) {
                $this->checkMiddleware($route);
                continue;
            }
            if ($method === StandardRoute::ADD_TYPE) {
                $this->checkTemplate($route);
                continue;
            }
            if ($method === StandardRoute::MODULE_TYPE) {
                $this->checkModule($route);
            }
        }
        return true;
    }

    /**
     * @throws RouteColoredException
     */
    private function checkController(array $route): void
    {
        if ((\str_contains($route['class'], '<') === false) && (\str_contains($route['class'], '[') === false)) {
            if (DynamicParams::isDebug() && !\class_exists($route['class'])) {
                $this->error(AsyncRouteException::HL35_ERROR, ["class" => "{$route['class']}"]);
            }
        }
    }

    /**
     * @throws RouteColoredException
     */
    private function checkPage(array $route): void
    {
        $this->checkController($route);
        $name = $route['name'];
        if (!$name || !preg_match('/^[A-Za-z0-9\-]+$/', $name)) {
            $this->error(AsyncRouteException::HL30_ERROR);
        }
        $relPath = "@/config/structure/$name.php";
        $path = SystemSettings::getRealPath($relPath);
        if (!$path || !SystemSettings::getRealPath('@library/adminpan')) {
            $this->error(AsyncRouteException::HL31_ERROR, ['name' => $name, 'path' => "@/config/structure/$name.php"]);
        }
    }

    /**
     * @throws RouteColoredException
     */
    private function checkMiddleware(array $route): void
    {
        if (DynamicParams::isDebug() && !\class_exists($route['class'])) {
            $this->error(AsyncRouteException::HL35_ERROR, ["class" => "{$route['class']}"]);
        }
    }

    /**
     * @throws RouteColoredException
     */
    private function checkTemplate(array $route): void
    {
        if (!\is_array($route['data']['view'])) {
            return;
        }
        $template = $route['data']['view']['template'];
        if (\str_contains($template, '<')) {
            return;
        }

        if (!SystemSettings::getRealPath("@views/$template") && !SystemSettings::getRealPath("@views/$template.php")) {
            $this->error(AsyncRouteException::HL16_ERROR, ["path" => "@/resources/views/$template", 'method' => $route['name']]);
        }
    }

    /**
     * @throws RouteColoredException
     */
    private function checkModule(array $route): void
    {
        $class = $route['class'];
        if ((\str_contains($class, '<') === false) && (\str_contains($class, '[') === false)) {
            if (!\str_starts_with($class, Settings::getParam('system', 'module.namespace'))) {
                $this->error(AsyncRouteException::HL35_ERROR, ["class" => "$class"]);
            }
        }
        $this->checkController($route);
    }

    /**
     * @throws RouteColoredException
     */
    private function error(string $tag, array $replace = []): void
    {
        throw (new RouteColoredException($tag))->complete(DynamicParams::isDebug(), $replace);
    }
}
