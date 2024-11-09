<?php

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\HttpMethods\Intelligence\Cookies\AsyncCookies;
use Hleb\HttpMethods\Intelligence\Cookies\StandardCookies;
use Hleb\HttpMethods\Specifier\DataType;
use Hleb\Main\Insert\ContainerUniqueItem;

/**
 * More convenient than the classic Cookies handling.
 * Can be deleted by previously set name.
 * In asynchronous mode, you only need to use this way
 * of working with Cookies.
 *
 * Более удобная, чем классическая, обработка Cookies.
 * Можно удалять по ранее установленному названию.
 * В асинхронном режиме нужно использовать только этот способ
 * работы с Cookies.
 */
#[Accessible] #[AvailableAsParent]
class CookieReference extends ContainerUniqueItem implements CookieInterface, Interface\Cookie, RollbackInterface
{
    /**
     * Contains the name of the required class depending on the type of use of the framework.
     *
     * Содержит название нужного класса в зависимости от типа использования фреймворка.
     *
     * @var StandardCookies $performer
     */
    private static string $performer;

    public function __construct()
    {
        self::$performer = SystemSettings::isAsync() ? AsyncCookies::class : StandardCookies::class;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function set(string $name, string $value = '', array $options = []): void
    {
        if (empty($options['path'])) {
            $options['path'] = '/';
        }

        self::$performer::set($name, $value, $options);
    }

    /** @inheritDoc */
    #[\Override]
    public function get(string $name): DataType
    {
        return self::$performer::get($name);
    }

    /**
     * @inheritDoc
     *
     * @return DataType[]
     */
    #[\Override]
    public function all(): array
    {
        return self::$performer::all();
    }

    /** @inheritDoc */
    #[\Override]
    public function setSessionName(string $name): void
    {
        self::$performer::setSessionName($name);
    }

    /** @inheritDoc */
    #[\Override]
    public function getSessionName(): string
    {
        return self::$performer::getSessionName();
    }

    /** @inheritDoc */
    #[\Override]
    public function setSessionId(string $id): void
    {
        self::$performer::setSessionId($id);
    }

    /** @inheritDoc */
    #[\Override]
    public function getSessionId(): string
    {
        return self::$performer::getSessionId();
    }

    /** @inheritDoc */
    #[\Override]
    public function delete(string $name): void
    {
        self::$performer::delete($name);
    }

    /** @inheritDoc */
    #[\Override]
    public function clear(): void
    {
        self::$performer::clear();
    }

    /** @inheritDoc */
    #[\Override]
    public static function rollback(): void
    {
        self::$performer::rollback();
    }

    /** @inheritDoc */
    #[\Override]
    public function has(string $name): bool
    {
        return self::$performer::get($name)->value() !== null;
    }

    /** @inheritDoc */
    #[\Override]
    public function exists(string $name): bool
    {
        $value = self::$performer::get($name)->value();

        return $value !== '' && $value !== null;
    }
}
