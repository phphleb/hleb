<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Reference\ViewInterface;

final class View
{
    private static ViewInterface|null $replace = null;

    /**
     * @param string $template - path to the template in the resources/views folder (or modules/{module_name}/views
     *                            in a module). If it is a PHP file, then the extension does not need to be specified.
     *
     *                         - путь до шаблона в папке resources/views (или modules/{module_name}/views в модуле).
     *                            Если это PHP-файл, то расширение указывать не нужно.
     *
     * @param array $params - The named parameters will be passed to the assigned template as variables.
     *
     *                       - Именованные параметры будут переданы в назначенный шаблон как переменные.
     *
     * @param int|null $status - For some simple tasks, you can immediately assign an HTTP response code.
     *
     *                         - Для некоторых простых задач, можно сразу назначить HTTP-код ответа.
     *
     * @return \Hleb\Constructor\Data\View
     *
     * @see view()
     *
     * @internal
     */
    public static function view(string $template, array $params = [], ?int $status = null): \Hleb\Constructor\Data\View
    {
        if (self::$replace) {
            return self::$replace->view($template, $params, $status);
        }

        $template = \str_replace('\\', '/', trim($template, '/\\'));

        return new \Hleb\Constructor\Data\View($template, $params, $status);
    }

    /**
     * @internal
     *
     * @see ViewForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(ViewInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
