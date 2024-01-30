<?php

namespace App\Bootstrap\Services;

/**
 * Demo interface for a custom service.
 * The service has a method to get the request id.
 * Using this interface, you can get a service.
 *
 * Демонстрационный интерфейс для пользовательского сервиса.
 * Сервис имеет метод для получения текущего Request ID.
 * По этому интерфейсу можно получить сервис.
 */
interface RequestIdInterface
{
    public function get(): string;
}
