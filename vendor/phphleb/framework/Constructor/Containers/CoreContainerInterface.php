<?php

namespace Hleb\Constructor\Containers;

use Hleb\Reference\ArrInterface;
use Hleb\Reference\CacheInterface;
use Hleb\Reference\CommandInterface;
use Hleb\Reference\ConverterInterface;
use Hleb\Reference\CsrfInterface;
use Hleb\Reference\DbInterface;
use Hleb\Reference\DebugInterface;
use Hleb\Reference\DtoInterface;
use Hleb\Reference\LogInterface;
use Hleb\Reference\PathInterface;
use Hleb\Reference\RedirectInterface;
use Hleb\Reference\SessionInterface;
use Hleb\Reference\CookieInterface;
use Hleb\Reference\RequestInterface;
use Hleb\Reference\ResponseInterface;
use Hleb\Reference\RouterInterface;
use Hleb\Reference\SettingInterface;
use Hleb\Reference\SystemInterface;
use Hleb\Reference\TemplateInterface;

interface CoreContainerInterface
{
    /**
     * Returns an initialized object from the container.
     *
     * Возвращает инициализированный объект из контейнера.
     *
     * @template TContainerInterface
     * @param class-string<TContainerInterface> $id
     * @return TContainerInterface|mixed
     */
    public function get(string $id): mixed;

    /**
     * Checks that such a container ID exists.
     *
     * Проверяет, что такой идентификатор контейнера существует.
     */
    public function has(string $id): bool;

    /**
     * Returns an object for array operations.
     *
     * Возвращает объект для операций с массивами.
     */
    public function arr(): ArrInterface;

    /**
     * Returns an object with methods for converting file paths.
     *
     * Возвращает объект с методами для преобразования файловых путей.
     */
    public function path(): PathInterface;

    /**
     * Returns an object with methods for performing a redirect.
     *
     * Возвращает объект с методами для осуществления редиректа.
     */
    public function redirect(): RedirectInterface;

    /**
     * Returns an object with methods to protect against CSRF attacks.
     *
     * Возвращает объект с методами защиты от CSRF-атак.
     */
    public function csrf(): CsrfInterface;

    /**
     * Returns the Cookies object from the container.
     *
     * Возвращает объект Cookies из контейнера.
     */
    public function cookies(): CookieInterface;

    /**
     * Returns the Request object from the container.
     *
     * Возвращает объект Request из контейнера.
     */
    public function request(): RequestInterface;

    /**
     * Returns the Response object from the container.
     *
     * Возвращает объект Response из контейнера.
     */
    public function response(): ResponseInterface;

    /**
     * Returns the framework settings from the container.
     *
     * Возвращает настройки фреймворка из контейнера.
     */
    public function settings(): SettingInterface;

    /**
     * Returns an object with actions for routes.
     *
     * Возвращает объект с действиями для маршрутов.
     */
    public function route(): RouterInterface;

    /**
     * Returns an object with the ability to exchange data
     * between controllers.
     *
     * Возвращает объект с возможностью обмена данными
     * между контроллерами.
     */
    public function dto(): DtoInterface;

    /**
     * Returns an object for managing sessions.
     *
     * Возвращает объект для управления сессиями.
     */
    public function session(): SessionInterface;

    /**
     * Returns an object to assign debug information to.
     *
     * Возвращает объект для назначения отладочной информации.
     */
    public function debug(): DebugInterface;

    /**
     * Returns an object with logging methods.
     *
     * Возвращает объект с методами логирования.
     */
    public function log(): LogInterface;

    /**
     * Returns the Database object from the container.
     *
     * Возвращает объект базы данных из контейнера.
     */
    public function db(): DbInterface;


    /**
     * Returns an object with highly specialized system methods.
     *
     * Возвращает объект с узкоспециальными системными методами.
     */
    public function system(): SystemInterface;

    /**
     * A wrapper for receiving objects in PSR format.
     *
     * Обёртка для получения объектов в формате PSR.
     */
    public function converter(): ConverterInterface;

    /**
     * Returns an object for working with cached data.
     *
     * Возвращает объект для работы с кешированными данными.
     */
    public function cache(): CacheInterface;

    /**
     * Returns an object for interacting with templates.
     *
     * Возвращает объект для взаимодействия с шаблонами.
     */
    public function template(): TemplateInterface;

    /**
     * Execute an initiated command object with arguments.
     *
     * Выполнение инициированного объекта команды с аргументами.
     */
    public function command(): CommandInterface;
}
