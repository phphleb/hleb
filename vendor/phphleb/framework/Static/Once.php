<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Constructor\Cache\OnceResult;
use Hleb\CoreProcessException;
use Hleb\Main\Insert\BaseAsyncSingleton;
use Hleb\Reference\OnceInterface;

final class Once extends BaseAsyncSingleton implements RollbackInterface
{
    private static OnceInterface|null $replace = null;

    /**
     * Returns execution result data for the once() function, in which one specific function (by line number in the file)
     * executes the passed callback function only once during the request, and upon subsequent calls to it, returns the previous result.
     * In this case, you need to avoid a situation where two or more once() functions are located in the same file on the same line.
     *
     * Возвращает данные результата выполнения для функции once(), при котором одна конкретная функция (по номеру строки в файле)
     * выполняет переданную callback-функцию только один раз в течении запроса, при последующих обращениях к ней возвращает
     * предыдущий результат.
     * При этом нужно избежать ситуации, когда две или более функции once() расположены в одном файле на одной строке.
     *
     * @see once()
     *
     * @internal
     */
    public static function get(callable $func): mixed
    {
        if (self::$replace) {
            return self::$replace->get($func);
        }
        return OnceResult::get($func);
    }

    /**
     * @inheritDoc
     *
     * @internal
     */
    #[\Override]
    public static function rollback(): void
    {
        if (self::$replace) {
            self::$replace->rollback();
        } else {
            OnceResult::rollback();
        }
    }

    /**
     * @internal
     *
     * @see OnceForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(OnceInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
