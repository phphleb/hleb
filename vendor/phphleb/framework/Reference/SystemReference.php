<?php

namespace Hleb\Reference;

use Hleb\Base\Task;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Constructor\Attributes\Disabled;
use Hleb\Constructor\Attributes\Task\Purpose;
use Hleb\Constructor\Cache\RouteMark;
use Hleb\Constructor\Data\DebugAnalytics;
use Hleb\Constructor\Data\DynamicParams;
use Hleb\Constructor\Data\MainLogLevel;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\Database\PdoManager;
use Hleb\Database\StandardDB;
use Hleb\Database\SystemDB;
use Hleb\Helpers\AttributeHelper;
use Hleb\HlebBootstrap;
use Hleb\InvalidArgumentException;
use Hleb\Main\Console\ConsoleHandler;
use Hleb\Main\Console\WebConsole;
use Hleb\Main\Insert\ContainerUniqueItem;
use Hleb\Main\Logger\LogLevel;
use Hleb\Main\System\LibraryServiceAddress;
use Hleb\Static\Settings;


/**
 * Various system calls for use by native framework libraries.
 *
 * Различные системные вызовы для использования собственными библиотеками фреймворка.
 */
#[Accessible] #[AvailableAsParent]
class SystemReference extends ContainerUniqueItem implements SystemInterface, Interface\System
{
    /** @inheritDoc */
    #[\Override]
    public function getRouteName(): ?string
    {
        return DynamicParams::getRouteName();
    }

    /** @inheritDoc */
    #[\Override]
    public function getActualLogLevel(): string
    {
        return MainLogLevel::get();
    }

    /** @inheritDoc */
    #[\Override]
    public function getLogLevelList(): array
    {
        return LogLevel::ALL;
    }

    /** @inheritDoc */
    #[\Override]
    public function getRouteCacheData(): array
    {
        $class = DynamicParams::getRouteClassName();
        if (!$class || !\class_exists($class, false)) {
            return [];
        }
        /** @var object $class */
        $data = $class::getData();
        if (!$data) {
            return [];
        }
        return $data;
    }

    /** @inheritDoc */
    #[\Override]
    public function getRouteCacheInfo(): array
    {
        $infoClassName = RouteMark::getRouteClassName(RouteMark::INFO_CLASS_NAME);
        if (!class_exists($infoClassName, false)) {
            $file = SystemSettings::getRealPath("@storage/cache/routes/{$infoClassName}.php");
            if ($file) {
                require $file;
            }
        }
        /** @var object $infoClassName */
        return $infoClassName::getData();
    }

    /** @inheritDoc */
    #[\Override]
    public function getStartTime(): ?float
    {
        return DynamicParams::getStartTime();
    }

    /** @inheritDoc */
    #[\Override]
    public function getEndTime(): ?float
    {
        return DynamicParams::getEndTime();
    }

    /** @inheritDoc */
    #[\Override]
    public function getCoreEndTime(): ?float
    {
        return DynamicParams::getCoreEndTime();
    }

    /** @inheritDoc */
    #[\Override]
    public function getRequestId(): string
    {
        return DynamicParams::getDynamicRequestId();
    }

    /** @inheritDoc */
    #[\Override]
    public function getLibraryKey(): string
    {
        return LibraryServiceAddress::KEY;
    }

    /** @inheritDoc */
    #[\Override]
    public function getDataFromDA(?string $key = null): array
    {
        if (!Settings::isDebug()) {
            return [];
        }
        if ($key === null) {
            return DebugAnalytics::getData();
        }
        return DebugAnalytics::getData()[$key] ?? [];
    }

    /** @inheritDoc */
    #[\Override]
    public function getClassesAutoloadDataFromDA(): array
    {
        return $this->getDataFromDA(DebugAnalytics::CLASSES_AUTOLOAD);
    }

    /** @inheritDoc */
    #[\Override]
    public function getInsertTemplateDataFromDA(): array
    {
        return $this->getDataFromDA(DebugAnalytics::INSERT_TEMPLATE);
    }

    /** @inheritDoc */
    #[\Override]
    public function getMiddlewareDataFromDA(): array
    {
        return $this->getDataFromDA(DebugAnalytics::MIDDLEWARE);
    }

    /** @inheritDoc */
    #[\Override]
    public function getInitiatorDataFromDA(): array
    {
        return $this->getDataFromDA(DebugAnalytics::INITIATOR);
    }

    /** @inheritDoc */
    #[\Override]
    public function getDebugDataFromDA(): array
    {
        return $this->getDataFromDA(DebugAnalytics::DATA_DEBUG);
    }

    /** @inheritDoc */
    #[\Override]
    public function getHlCheckDataFromDA(): array
    {
        return $this->getDataFromDA(DebugAnalytics::HL_CHECK);
    }

    /** @inheritDoc */
    #[\Override]
    public function getDbDebugDataFromDA(): array
    {
        return $this->getDataFromDA(DebugAnalytics::DB_DEBUG);
    }

    /** @inheritDoc */
    #[\Override]
    public function getHlebVersionAsConsoleFormat(): string
    {
        return (new ConsoleHandler())->getVersion();
    }

    /** @inheritDoc */
    #[\Override]
    public function getRoutesAsConsoleFormat(): string
    {
        return (new ConsoleHandler())->getRoutes();
    }

    /** @inheritDoc */
    #[\Override]
    public function getPdoManager(#[\SensitiveParameter] ?string $configKey = null): PdoManager
    {
        return StandardDB::getStandardPdoInstance($configKey);
    }

    /** @inheritDoc */
    #[\Override]
    public function updateRouteCacheAsConsoleFormat(): string
    {
        return (new ConsoleHandler())->updateRouteCache();
    }

    /** @inheritDoc */
    #[\Override]
    public function getFrameworkApiVersion(): string
    {
        return \implode(\array_map(function($v){
            return \str_pad($v, 3, '0', STR_PAD_LEFT);
        }, \explode('.', HLEB_CORE_VERSION)));
    }

    /** @inheritDoc */
    #[\Override]
    public function getFrameworkResourcePrefix(): string
    {
        return LibraryServiceAddress::KEY;
    }

    /** @inheritDoc */
    #[\Override]
    public function getFrameworkVersion(): string
    {
        return HLEB_CORE_VERSION;
    }

    /** @inheritDoc */
    #[\Override]
    public function getVersion(): string
    {
        return HLEB_CORE_VERSION;
    }

    /** @inheritDoc */
    #[\Override]
    public function getApiVersion(): string
    {
        return self::getFrameworkApiVersion();
    }

    /** @inheritDoc */
    #[\Override]
    public function isWebConsoleActive(): bool
    {
        return WebConsole::isUsed();
    }

    /** @inheritDoc */
    #[\Override]
    public function createSqlQueryLog(float $startTime, string $query, ?string $configKey = null, string $tag = 'special', ?string $driver = null,): void
    {
        $configKey === null and $configKey = SystemSettings::getValue('database', 'base.db.type');

        SystemDB::createLog($startTime, $query, $configKey, $tag, driver: $driver);
    }

    /** @inheritDoc */
    public function createCustomLog(
        #[\SensitiveParameter] string $sql,
        float $microtime,
        array $params = [],
        ?string $dbname = null,
        ?string $driver = null,
    ): void
    {
        SystemDB::createCustomLog($sql, $microtime, $params, $dbname, $driver);
    }

    /** @inheritDoc */
    public static function getTaskPermissions(string $taskClass): array
    {
        $parentClass = Task::class;
        if (!\is_subclass_of($taskClass, $parentClass)) {
            throw new InvalidArgumentException("{$taskClass} is not a subclass of {$parentClass}.");
        }
        $allPermissions = [HlebBootstrap::CONSOLE_MODE, HlebBootstrap::ASYNC_MODE, HlebBootstrap::STANDARD_MODE];

        $helper = new AttributeHelper($taskClass);

        if ($helper->hasClassAttribute(Disabled::class)) {
            return [];
        }
        if (!$helper->hasClassAttribute(Purpose::class)) {
            return $allPermissions;
        }

        $status = $helper->getClassValue(Purpose::class, 'status');

        if ($status === Purpose::FULL) {
            return $allPermissions;
        }

        if ($status === Purpose::CONSOLE) {
            return [HlebBootstrap::CONSOLE_MODE];
        }

        if ($status === Purpose::EXTERNAL) {
            return [HlebBootstrap::ASYNC_MODE, HlebBootstrap::STANDARD_MODE];
        }

        return [];
    }
}
