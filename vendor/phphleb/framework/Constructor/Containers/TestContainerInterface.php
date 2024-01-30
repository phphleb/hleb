<?php

namespace Hleb\Constructor\Containers;

/**
 * Interface for replacing methods of the Hleb\Static\Container class in tests.
 *
 * Интерфейс для подмены методов класса Hleb\Static\Container в тестах.
 */
interface TestContainerInterface
{
    /**
     * Getting a custom container by ID.
     *
     * Получение пользовательского контейнера по идентификатору.
     */
    public function get(string $id);

    /**
     * Checking if a container exists by ID.
     *
     * Проверка существования контейнера по ID.
     */
    public function has(string $id): bool;

    /**
     * Returns the configured Model container or its replacement (for tests).
     *
     * Возвращает сконфигурированный контейнер Модели или его замену (для тестов).
     */
    public static function getContainer(): \App\Bootstrap\ContainerInterface;

    /**
     * (!)Forced cleaning of containers.
     * Can be used at the end of an asynchronous request to check for problems
     * with containers. Otherwise, the use of this operation is not recommended.
     * Cleaning the contents of containers is already implemented in the framework.
     *
     * (!)Принудительная очистка контейнеров.
     * Может применяться в конце асинхронного запроса для проверки проблем
     * с контейнерами. В остальном применение этой операции не рекомендуется.
     * Очистка содержимого контейнеров уже реализована во фреймворке.
     */
    public static function rollback(): void;
}
