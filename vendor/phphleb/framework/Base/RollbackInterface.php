<?php

namespace Hleb\Base;

/**
 * When this interface is found on the class being used
 * in asynchronous mode, the rollback() method is called at the end of each request,
 * which helps clean up the class state.
 * You must manually add actions to reset/rollback class state to the method.
 * It is worth considering that if a class has a parent class with this interface
 * and the rollback() method is overridden, then it will be called twice.
 * Therefore, if you need to perform an action to complete an asynchronous request,
 * use it in the rollback() of the App\Bootstrap\ContainerFactory class.
 *
 * При нахождении этого интерфейса у используемого класса
 * в асинхронном режиме вызывается метод rollback() в конце каждого запроса,
 * что способствует очистке состояния класса.
 * Необходимо вручную добавить действия по очистке/откату состояния класса в метод.
 * Стоит учесть, что если класс имеет родительский класс с этим интерфейсом,
 * то метод rollback() будет вызван дважды.
 * Поэтому, если необходимо выполнить действие по завершению асинхронного запроса,
 * используйте его в rollback() класса App\Bootstrap\ContainerFactory.
 */
interface RollbackInterface
{
    /**
     * Used to clear class state at the end of a request for asynchronous mode.
     * If an object of a derived class uses lazy loading in the container,
     * then you must ensure that the fields involved have default values.
     * Example:
     *
     * Используется для очистки состояния класса в конце запроса для асинхронного режима.
     * Если объект наследуемого класса использует "ленивую" загрузку в контейнере,
     * то необходимо убедиться, что задействованные поля имеют значения по умолчанию.
     * Пример:
     *
     * ```php
     * class Example implements \Hleb\Base\RollbackInterface
     * {
     *    private static ?User $currentUser = null;
     *
     *    public function __construct(User $user) {
     *      self::$currentUser = $user;
     *    }
     *
     *    #[\Override]
     *    public static function rollback(): void {
     *       self::$currentUser = null;
     *    }
     * }
     *
     * ```
     */
    public static function rollback(): void;
}
