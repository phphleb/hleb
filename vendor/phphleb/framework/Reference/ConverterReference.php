<?php

declare(strict_types=1);

namespace Hleb\Reference;

use App\Bootstrap\BaseContainer;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Phphleb\PsrAdapter\Psr11\Container;
use Phphleb\PsrAdapter\Psr11\IntermediateContainerInterface;
use Phphleb\PsrAdapter\Psr16\Cache;
use Phphleb\PsrAdapter\Psr3\Logger;

#[Accessible] #[AvailableAsParent]
class ConverterReference implements ConverterInterface, Interface\Converter
{

    /** @inheritDoc */
    #[\Override]
    public function toPsr3Logger(): \Psr\Log\LoggerInterface
    {
        self::detection('Psr\Log\LoggerInterface', Logger::class, 'log');

        return new Logger();
    }

    /**
     * @inheritDoc
     *
     * @return IntermediateContainerInterface
     */
    #[\Override]
    public function toPsr11Container(): \Psr\Container\ContainerInterface
    {
        self::detection('Psr\Container\ContainerInterface', Container::class, 'container');

        return new Container(BaseContainer::instance());
    }


    /**
     * @inheritDoc
     */
    #[\Override]
    public function toPsr16SimpleCache(): \Psr\SimpleCache\CacheInterface
    {
        self::detection('Psr\SimpleCache\CacheInterface', Cache::class, 'simple-cache');

        return new Cache();
    }

    /**
     * Issues a warning if the included library is missing.
     *
     * Выдает предупреждение, если не хватает подключённой библиотеки.
     */
    private static function detection(string $interface, string $container, string $name): void
    {
        if (!\interface_exists($interface)) {
            throw new \RuntimeException("Interface $interface not found. You need to install it from the psr/$name library.");
        }
        if (!\class_exists($container)) {
            throw new \RuntimeException("Class $container not found. You need to install the phphleb/psr-adapter library.");
        }
    }
}
