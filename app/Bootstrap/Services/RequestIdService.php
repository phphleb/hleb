<?php

namespace App\Bootstrap\Services;

use App\Bootstrap\BaseContainer;
use App\Bootstrap\ContainerFactory;
use App\Bootstrap\ContainerInterface;
use Hleb\Static\System;

/**
 * Demo class for creating a service.
 * The service has a method to get the request id.
 *
 * Демонстрационный класс для создания сервиса.
 * Сервис имеет метод для получения текущего Request ID.
 */
class RequestIdService implements RequestIdInterface
{
    /**
     * @see BaseContainer::requestId() - receiving from the container using the designated method.
     *                                 - получение из контейнера по назначенному методу.
     *
     * @see ContainerInterface::requestId() - assignment of a method in the container interface.
     *                                       - назначение метода в интерфейсе контейнера.
     *
     * @see ContainerFactory::getSingleton() - setting an object of this class as a singleton
     *                                         in the container.
     *
     *                                       - установка объекта этого класса в качестве
     *                                         singleton в контейнере.
     */
    #[\Override]
    public function get(): string
    {
        return System::getRequestId();
    }
}
