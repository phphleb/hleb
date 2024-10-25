<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Main\Insert\BaseAsyncSingleton;
use Hleb\Reference\RedirectInterface;

#[Accessible]
final class Redirect extends BaseAsyncSingleton implements RollbackInterface
{
    private static RedirectInterface|null $replace = null;

    /**
     * Redirect to internal page or full URL.
     *
     * Редирект на внутреннюю страницу или полный URL.
     *
     * @param string $location - redirect target, full or relative URL.
     *                         - цель редиректа, полный или относительный URL.
     *
     * @param int $status - response code of the current HTTP request for the redirect.
     *                    - код ответа текущего HTTP-запроса для редиректа.
     */
    public static function to(string $location, int $status = 302): void
    {
        if (self::$replace) {
            self::$replace->to($location, $status);
        } else {
            BaseContainer::instance()->get(RedirectInterface::class)->to($location, $status);
        }
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
            self::$replace::rollback();
        } else {
            BaseContainer::instance()->get(RedirectInterface::class)::rollback();
        }
    }

    /**
     * @internal
     *
     * @see RedirectForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(RedirectInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
