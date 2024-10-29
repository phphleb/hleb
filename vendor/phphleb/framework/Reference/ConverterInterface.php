<?php

namespace Hleb\Reference;

use Phphleb\PsrAdapter\Psr11\IntermediateContainerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

interface ConverterInterface
{
    /**
     * Returns a wrapper around the logger in a framework based on the PSR-3 interface.
     *
     * Возвращает обертку над механизмом логирования во фреймворке на основе PSR-3 интерфейса.
     */
    public function toPsr3Logger(): LoggerInterface;

    /**
     * Wrapper for container implementation according to RPS-11 rules.
     *
     * Обёртка для реализации контейнера по правилам PSR-11.
     *
     * @return IntermediateContainerInterface
     */
    public function toPsr11Container(): ContainerInterface;

    /**
     * Returns a wrapper over an object for caching in a framework based on the PSR-16 interface.
     *
     * Возвращает обертку над объектом для кеширования во фреймворке на основе PSR-16 интерфейса.
     */
    public function toPsr16SimpleCache(): \Psr\SimpleCache\CacheInterface;
}
