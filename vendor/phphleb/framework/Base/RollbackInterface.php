<?php

declare(strict_types=1);

namespace Hleb\Base;

/**
 * When this interface is found on the class being used
 * in asynchronous mode, the rollback() method is called at the end of each request,
 * which helps clean up the class state.
 * You must manually add actions to reset/rollback class state to the method.
 *
 * При нахождении этого интерфейса у используемого класса
 * в асинхронном режиме вызывается метод rollback() в конце каждого запроса,
 * что способствует очистке состояния класса.
 * Необходимо вручную добавить действия по очистке/откату состояния класса в метод.
 */
interface RollbackInterface
{
    /**
     * Used to clear class state at the end of a request for asynchronous mode.
     * Example:
     *
     * Используется для очистки состояния класса в конце запроса для асинхронного режима.
     * Пример:
     *
     * ```php
     * class Example implements \Hleb\Base\RollbackInterface
     * {
     *    private static User $currentUser = null;
     *
     *    public function set(User $user): void {
     *      self::currentUser = $user;
     *    }
     *
     *    #[\Override]
     *    public static function rollback(): void {
     *       self::currentUser = null;
     *    }
     * }
     *
     * ```
     */
    public static function rollback(): void;
}
