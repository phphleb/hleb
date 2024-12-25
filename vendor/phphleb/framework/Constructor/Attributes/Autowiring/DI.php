<?php

declare(strict_types=1);

namespace Hleb\Constructor\Attributes\Autowiring;

/**
 * Provides the ability to set the required value for auto-substitution
 * from a container using the Dependency Injection method.
 * Can be used to automatically resolve dependencies.
 *
 * Предоставляет возможность задать необходимое значение для авто-подстановки
 * из контейнера методом Dependency Injection.
 * Может быть применен для автоматического разрешения зависимостей.
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
class DI
{
    /**
     * Setting up a substitution for the Dependency Injection parameter.
     * It must be remembered that creating a new class cancels its uniqueness
     * in the container (if it is there as a singleton)
     * and the new class will be applied.
     * `null` is required if the parameter allows it
     * without a default value.
     * Examples of use:
     *
     * Установка подмены для Dependency Injection параметра.
     * Необходимо помнить, что создание нового класса отменяет
     * его уникальность в контейнере (если он там есть как singleton)
     * и будет применен новый класс.
     * Значение `null` нужно, если параметр допускает его
     * без значения по умолчанию.
     * Примеры использования:
     *
     * ```php
     * class ExampleController extends Controller
     * {
     *     public function index(
     *         #[DI(LocalFileStorage::class)]
     *         FileSystemInterface $storage,
     *
     *         #[DI('\App\Notification\JwtAuthenticator')]
     *         AuthenticatorInterface $authenticator,
     *
     *         #[DI(new EmailNotificationSender())]
     *         NotificationSenderInterface $notificationSender,
     *     ) {
     *       //...//
     *     }
     * }
     *
     * ```
     *
     *
     * @param string|object|null $classNameOrObject - object or name of the class/interface
     *                                                that will be created from the container.
     *
     *                                              - объект или название класса/интерфейсa,
     *                                                который будет создан из контейнера.
     */
    public function __construct(public string|object|null $classNameOrObject)
    {
    }
}
