<?php

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\CoreProcessException;
use Hleb\Main\Insert\ContainerUniqueItem;
use Hleb\Static\Cookies;

#[Accessible] #[AvailableAsParent]
class SessionReference extends ContainerUniqueItem implements SessionInterface, Interface\Session, RollbackInterface
{

    /**
     * Immutable identifier for flash sessions.
     *
     * Неизменяемый идентификатор для flash-сессий.
     */
    protected const FLASH_ID = "_hl_flash_";


    /** @inheritDoc */
    #[\Override]
    public function all(): array
    {
        $all =  $_SESSION ?? [];
        unset($all[self::FLASH_ID]);

        return $all;
    }

    /** @inheritDoc */
    #[\Override]
    public function get(int|string $name, mixed $default = null): mixed
    {
        $all =  $_SESSION ?? [];
        unset($all[self::FLASH_ID]);

        $result = $all[$name] ?? null;
        if (!\is_null($result)) {
            return $result;
        }
        if (\is_callable($default)) {
            return $default();
        }
        return $default;
    }

    /** @inheritDoc */
    #[\Override]
    public function set(int|string $name, float|int|bool|array|string|null $data): void
    {
        if ($name === self::FLASH_ID) {
            throw new CoreProcessException('You cannot directly change the value of a special identifier for flash sessions.');
        }
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
        if ($name === self::FLASH_ID) {
            throw new CoreProcessException('You cannot directly delete the value of a special identifier for flash sessions.');
        }
        if (\array_key_exists($name, $_SESSION ?? [])) {
            unset($_SESSION[$name]);
        }
    }

    /** @inheritDoc */
    #[\Override]
    public function clear(): void
    {
        foreach($_SESSION ?? [] as $name => $item) {
            unset($_SESSION[$name]);
        }
    }

    /** @inheritDoc */
    #[\Override]
    public static function rollback(): void
    {
        foreach($_SESSION ?? [] as $name => $item) {
            unset($_SESSION[$name]);
        }
    }

    /** @inheritDoc */
    #[\Override]
    public function has(int|string $name): bool
    {
        return \array_key_exists($name, $_SESSION ?? []);
    }

    /** @inheritDoc */
    #[\Override]
    public function exists(int|string $name): bool
    {
        return isset($_SESSION[$name]) && $_SESSION[$name] !== '';
    }

    /** @inheritDoc */
    #[\Override]
    public function setFlash(string $name, float|int|bool|array|string|null $data, int $repeat = 1): void
    {
        if (!isset($_SESSION[self::FLASH_ID])) {
            $_SESSION[self::FLASH_ID] = [];
        }

        if (\is_null($data) || $repeat < 1) {
            unset($_SESSION[self::FLASH_ID][$name]);
        } else {
            $_SESSION[self::FLASH_ID][$name] = [
                'new' => $data,
                'old' => null,
                'reps_left' => $repeat,
            ];
        }

    }

    /** @inheritDoc */
    #[\Override]
    public function clearFlash(): void
    {
        $_SESSION[self::FLASH_ID] = [];
    }

    /** @inheritDoc */
    #[\Override]
    public function allFlash(): array
    {
        return $_SESSION[self::FLASH_ID] ?? [];
    }

    /** @inheritDoc */
    #[\Override]
    public function getFlash(string $name, string|float|int|array|bool|null $default = null): string|float|int|array|bool|null
    {
       return $_SESSION[self::FLASH_ID][$name]['old'] ?? $default;
    }

    /** @inheritDoc */
    #[\Override]
    public function getAndClearFlash(string $name, string|float|int|array|bool|null $default = null): string|float|int|array|bool|null
    {
        $value = $this->getFlash($name);
        if ($value !== null) {
            $this->setFlash($name, null);
            return $value;
        }
        return $default;
    }

    /** @inheritDoc */
    #[\Override]
    public function hasFlash(string $name, string $type = 'old'): bool
    {
        if ($type === 'all') {
            return !\is_null($_SESSION[self::FLASH_ID][$name] ?? null);
        }
        if ($type === 'new' || $type === 'old') {
            return !\is_null($_SESSION[self::FLASH_ID][$name][$type] ?? null);
        }
        throw new CoreProcessException('The flash type can only be `new`, `old` or `all`.');
    }

    /** @inheritDoc */
    #[\Override]
    public function increment(string $name, int $amount = 1): void
    {
        if ($amount <= 0) {
            throw new CoreProcessException('The increment must be greater than zero.');
        }
        $this->counter($name, $amount);
    }

    /** @inheritDoc */
    #[\Override]
    public function decrement(string $name, int $amount = 1): void
    {
        if ($amount <= 0) {
            throw new CoreProcessException('The decrement must be greater than zero.');
        }
        $this->counter($name, -$amount);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function counter(string $name, int $amount): void
    {
        if (!$this->has($name)) {
            $_SESSION[$name] = 0;
        }
        if (!\is_numeric($_SESSION[$name])) {
            throw new CoreProcessException('The value for the counter must be numeric.');
        }

        $_SESSION[$name] += $amount;
    }
}
