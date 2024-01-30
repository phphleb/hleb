<?php

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\Main\Insert\ContainerUniqueItem;
use Hleb\Static\Cookies;

#[Accessible] #[AvailableAsParent]
class SessionReference extends ContainerUniqueItem implements SessionInterface, Interface\Session
{

    /** @inheritDoc */
    #[\Override]
    public function all(): array
    {
        return $_SESSION ?? [];
    }

    /** @inheritDoc */
    #[\Override]
    public function get(int|string $name): mixed
    {
        return $_SESSION[$name] ?? null;
    }

    /** @inheritDoc */
    #[\Override]
    public function set(int|string $name, float|int|bool|array|string|null $data): void
    {
        $_SESSION[$name] = $data;
    }

    /**
     * Returns the current session identifier.
     *
     * Возвращает идентификатор текущей сессии.
     */
    #[\Override]
    public function getSessionId(): string|null
    {
        return (SystemSettings::isAsync() ? Cookies::getSessionId() : \session_id()) ?: null;
    }

    /** @inheritDoc */
    #[\Override]
    public function delete(int|string $name): void
    {
        if (\array_key_exists($name, $_SESSION ?? [])) {
            unset($_SESSION[$name]);
        }
    }

    /** @inheritDoc */
    #[\Override]
    public function clear(): void
    {
        self::rollback();
    }

    /** @inheritDoc */
    #[\Override]
    public static function rollback(): void
    {
        foreach($_SESSION ?? [] as $name => $item) {
            unset($_SESSION[$name]);
        };
    }
}
