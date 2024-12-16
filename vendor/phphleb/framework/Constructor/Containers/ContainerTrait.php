<?php

declare(strict_types=1);

namespace Hleb\Constructor\Containers;

use App\Bootstrap\BaseContainer;
use App\Bootstrap\ContainerInterface;
use Hleb\Reference\CookieInterface;
use Hleb\Reference\RequestInterface;
use Hleb\Reference\ResponseInterface;
use Hleb\Reference\RouterInterface;
use Hleb\Reference\SettingInterface;

trait ContainerTrait
{
    /**
     * Required to call a container-based class inside another container-based class.
     * For example, the value can be passed as $this->config to a new command object
     * called inside the container class.
     *
     * Необходим для вызова класса на основе контейнера внутри другого такого класса.
     * Например, значение можно передать как $this->config в новый объект команды
     * вызываемой внутри класса с контейнером.
     */
    protected readonly array $config;

    /**
     * The current initialized set of containers,
     * from which you can get the required by name
     * interface or assigned container name.
     *
     * Текущий инициализированный набор контейнеров,
     * из которого можно получить необходимый по названию
     * интерфейса или присвоенного названия контейнера.
     *
     * @see BaseContainer::get() - default initialization on demand
     *                             for custom containers.
     *                           - дефолтная инициализация по запросу
     *                             для пользовательских контейнеров.
     */
    protected readonly ContainerInterface $container;

    /**
     * Container constructor.
     * To override self::$container, you must pass it in the $config array
     * the required value of 'container' as a class that implements the interface
     * ContainerInterface.
     *
     * Конструктор контейнера.
     * Для переопределения self::$container необходимо передать в массиве $config
     * необходимое значение 'container' в виде класса, реализующего интерфейс
     * ContainerInterface.
     */
    public function __construct(#[\SensitiveParameter] array $config = [])
    {
        $this->config = $config;
        /*
         *  Can be used as $this->container->get(...).
         *
         *  Может быть использован как $this->container->get(...).
         */
        $this->container = $config['container'] ?? BaseContainer::instance();
    }

    /**
     * Returns the Cookies object from the container.
     *
     * Возвращает объект Cookies из контейнера.
     */
    final protected function cookies(): CookieInterface
    {
        return $this->container->get(CookieInterface::class);
    }

    /**
     * Returns the Request object from the container.
     *
     * Возвращает объект Request из контейнера.
     */
    final protected function request(): RequestInterface
    {
        return $this->container->get(RequestInterface::class);
    }

    /**
     * Returns the Response object from the container.
     *
     * Возвращает объект Response из контейнера.
     */
    final protected function response(): ResponseInterface
    {
        return $this->container->get(ResponseInterface::class);
    }

    /**
     * Returns the framework settings from the container.
     *
     * Возвращает настройки фреймворка из контейнера.
     */
    final protected function settings(): SettingInterface
    {
        return $this->container->get(SettingInterface::class);
    }

    /**
     * Returns an object with actions for routes.
     *
     * Возвращает объект с действиями для маршрутов.
     */
    final protected function router(): RouterInterface
    {
        return $this->container->get(RouterInterface::class);
    }
}
