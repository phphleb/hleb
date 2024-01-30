<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Main\Insert\BaseSingleton;

/**
 * Allows you to use test objects with a similar interface,
 * which are added to classes from the Static folder
 * and replace the execution of methods.
 *
 * Позволяет использовать тестовые объекты с аналогичным
 * интерфейсом, которые добавляются к классам из папки Static
 * и подменяют выполнение методов.
 */
abstract class BaseMockAddOn extends BaseSingleton
{
    /**
     * After replacing the methods of the object for testing,
     * you will need to return them to their basic execution.
     * This method rolls back the use of a test object that
     * was previously installed.
     * If you are using the Mockery library with phpUnit
     * to create test objects, you must use this method
     * instead of Mockery::close().
     *
     * После подмены методов объекта для тестирования
     * необходимо будет вернуть им базовое выполнение.
     * Этот метод откатывает использование тестового объекта,
     * установленного ранее.
     * Если вы используете библиотеку Mockery совместно
     * с phpUnit для создания тестовых объектов, то необходимо
     * использовать этот метод вместо Mockery::close().
     *
     * @see Mock::cancel() - rollback of all replacement objects.
     *                     - откат всех подменных объектов.
     */
    #[ForTestOnly]
    abstract public static function cancel(): void;
}
