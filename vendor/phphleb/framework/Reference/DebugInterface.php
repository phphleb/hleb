<?php

namespace Hleb\Reference;

/**
 * For backward compatibility with custom containers,
 * this interface can only be extended.
 *
 * Для обратной совместимостью с пользовательскими контейнерами
 * этот интерфейс может только расширяться.
 */
interface DebugInterface
{
    /**
     * Sends data for storage when debug mode is active.
     * By default, the data is displayed in the framework debug panel.
     *
     * При активном отладочном режиме отправляет данные для хранения.
     * По умолчанию данные выводятся в панель отладки фреймворка.
     */
    public function send(mixed $data, ?string $name = null): void;

    /**
     * When debug mode is active, returns any previously added debugging data.
     *
     * При активном режиме отладки возвращает все добавленные ранее отладочные данные.
     */
    public function getCollection(): array;

    /**
     * Saves the system tag to debug data.
     *
     * Сохраняет системную метку в отладочные данные.
     *
     * @see hl_check()
     */
    public function setHlCheck(string $message, ?string $file = null, ?int $line = null): void;

    /**
     * Returns the status of the debug mode activity.
     *
     * Возвращает статус активности режима отладки.
     */
    public function isActive(): bool;
}
