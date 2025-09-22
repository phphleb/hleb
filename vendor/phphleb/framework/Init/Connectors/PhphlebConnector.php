<?php

/*declare(strict_types=1);*/


/**
 * Adding a class autoload path: classname => realpath.
 *
 * Добавление пути для автозагрузки класса: classname => realpath.
 */

namespace Hleb\Init\Connectors;

/**
 * @internal
 */
final class PhphlebConnector
{
    public static array $map = [
        'Phphleb\Debugpan\Controllers\AppController' => '/phphleb/debugpan/Controllers/AppController.php',
        'Phphleb\Debugpan\Controllers\ResponseTrait' => '/phphleb/debugpan/Controllers/ResponseTrait.php',
        'Phphleb\Debugpan\Controllers\StateController' => '/phphleb/debugpan/Controllers/StateController.php',
        'Phphleb\Debugpan\InitPanel' => '/phphleb/debugpan/InitPanel.php',
        'Phphleb\Debugpan\Panel\Resources' => '/phphleb/debugpan/Panel/Resources.php',
        'Phphleb\Idnaconv\IdnaConvert' => '/phphleb/idnaconv/IdnaConvert.php',
        'Phphleb\Nicejson\JsonConverter' => '/phphleb/nicejson/JsonConverter.php',
        'Phphleb\TestO\Example\ExampleTest' => '/phphleb/test-o/example/ExampleTest.php',
        'Phphleb\TestO\TestCase' => '/phphleb/test-o/TestCase.php',
        'Phphleb\TestO\Tester' => '/phphleb/test-o/Tester.php',
        'Phphleb\TestO\Tests\ArrayEqualsTest' => '/phphleb/test-o/Tests/ArrayEqualsTest.php',
    ];

    public static function add(array $map): void
    {
        self::$map = \array_merge($map, self::$map);
    }
}
