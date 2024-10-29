<?php

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Reference\ConverterInterface;
use Phphleb\PsrAdapter\Psr11\IntermediateContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;


/**
 * Converting internal framework entities into PSR objects.
 *
 * Преобразование внутренних сущностей фреймворка в PSR-объекты.
 */
class Converter extends BaseSingleton
{
    private static ConverterInterface|null $replace = null;

    /**
     * Wrapper for container implementation according to RPS-11 rules.
     *
     * Обёртка для реализации контейнера по правилам PSR-11.
     *
     * @return IntermediateContainerInterface
     */
    public static function toPsr11Container(): \Psr\Container\ContainerInterface
    {
        if (self::$replace) {
            return self::$replace->toPsr11Container();
        }

        return BaseContainer::instance()->get(ConverterInterface::class)->toPsr11Container();
    }

    /**
     * Returns a wrapper around the logger in a framework based on the PSR-3 interface.
     *
     * Возвращает обертку над механизмом логирования во фреймворке на основе PSR-3 интерфейса.
     */
    public static function toPsr3Logger(): LoggerInterface
    {
        if (self::$replace) {
            return self::$replace->toPsr3Logger();
        }

        return BaseContainer::instance()->get(ConverterInterface::class)->toPsr3Logger();
    }

    /**
     * Returns a wrapper over an object for caching in a framework based on the PSR-16 interface.
     *
     * Возвращает обертку над объектом для кеширования во фреймворке на основе PSR-16 интерфейса.
     */
    public static function toPsr16SimpleCache(): CacheInterface
    {
        if (self::$replace) {
            return self::$replace->toPsr16SimpleCache();
        }

        return BaseContainer::instance()->get(ConverterInterface::class)->toPsr16SimpleCache();
    }

    /**
     * @internal
     *
     * @see ContainerForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(ConverterInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
