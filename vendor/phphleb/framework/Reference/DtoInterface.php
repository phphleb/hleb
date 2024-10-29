<?php

namespace Hleb\Reference;

/**
 * For backward compatibility with custom containers,
 * this interface can only be extended.
 *
 * Для обратной совместимостью с пользовательскими контейнерами
 * этот интерфейс может только расширяться.
 */
interface DtoInterface
{
    /**
     * Get the value from the previous controller by the assigned name.
     *
     * Получить значение из предыдущего контроллера по назначенному имени.
     */
    public function get($name);

    /**
     * Set value by name to use in next controller.
     *
     * Установить значение по имени для использования в следующем контроллере.
     */
    public function set($name, $value): void;

    /**
     * Data can be cleared as soon as it is no longer needed.
     *
     * Данные можно очистить, как только они становятся не нужны.
     */
    public function clear(): void;

    /**
     * Returns all assigned data in an array.
     *
     * Возвращает все присвоенные данные в массиве.
     */
    public function list(): array;
}
