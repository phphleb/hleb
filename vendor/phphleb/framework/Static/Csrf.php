<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Reference\CsrfInterface;

#[Accessible]
final class Csrf extends BaseSingleton
{
    private static CsrfInterface|null $replace = null;

    /**
     * The Csrf::token() function returns the protected token for protection against CSRF attacks.
     *
     * Функция Csrf::token() возвращает защищённый токен для защиты от CSRF-атак.
     */
    public static function token(): string
    {
        if (self::$replace) {
            return self::$replace->token();
        }

        return BaseContainer::instance()->get(CsrfInterface::class)->token();
    }

    /**
     * The Csrf::field() method returns HTML content to be inserted
     * into the form to protect against CSRF attacks.
     *
     * Метод Csrf::field() возвращает HTML-контент для вставки
     * в форму для защиты от CSRF-атак.
     */
    public static function field(): string
    {
        if (self::$replace) {
            return self::$replace->field();
        }

        return BaseContainer::instance()->get(CsrfInterface::class)->field();
    }

    /**
     * The framework checks the token for protection against CSRF.
     *
     * Проверка фреймворком токена для защиты от CSRF.
     */
    public static function validate(?string $key): bool
    {
        if (self::$replace) {
            return self::$replace->validate($key);
        }

        return BaseContainer::instance()->get(CsrfInterface::class)->validate($key);
    }

    /**
     * Returns the found token in the request data or null.
     *
     * Возвращает найденный токен в данных запроса или null.
     */
    public static function discover(): string|null
    {
        if (self::$replace) {
            return self::$replace->discover();
        }

        return BaseContainer::instance()->get(CsrfInterface::class)->discover();
    }

    /**
     * @internal
     *
     * @see CsrfForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(CsrfInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
