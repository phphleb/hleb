<?php

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Constructor\Data\DebugAnalytics;
use Hleb\Constructor\Data\DynamicParams;
use Hleb\Main\Insert\ContainerUniqueItem;
use Hleb\Static\Path;

#[Accessible] #[AvailableAsParent]
class DebugReference extends ContainerUniqueItem implements DebugInterface, Interface\Debug, RollbackInterface
{
    /** @inheritDoc */
    #[\Override]
    public function send(mixed $data, ?string $name = null): void
    {
        if (self::isActive()) {
            DebugAnalytics::addData(DebugAnalytics::DATA_DEBUG, [($name ?: 0) => $data]);
        }
    }

    /** @inheritDoc */
    #[\Override]
    public function getCollection(): array
    {
        if (self::isActive()) {
            return DebugAnalytics::getData()[DebugAnalytics::DATA_DEBUG] ?? [];
        }
        return [];
    }

    /** @inheritDoc */
    #[\Override]
    public function setHlCheck(string $message, ?string $file = null, ?int $line = null): void
    {
        if (self::isActive() && \defined('HLEB_START')) {
            $time = \round(\microtime(true) - HLEB_START, 5);
            $debug = DebugAnalytics::getData();
            $memory = \memory_get_usage();
            $prevTime = $time;
            $prevMemory = $memory;
            if (isset($debug[DebugAnalytics::HL_CHECK])) {
                $prevData = \end($debug[DebugAnalytics::HL_CHECK]);
                $lastData = \end($prevData);
                $prevTime = $lastData['time_sec'] ?? $time;
                $prevMemory = $lastData['memory_usage_bytes'] ?? $memory;
            }
            $data = [
                'position' => $file ? Path::relative($file) . ($line ? ':' . $line : '') : 'not specified',
                'memory_peak_usage_mb' => \round(\memory_get_peak_usage() / 1024 / 1024, 5),
                'memory_peak_real_usage_mb' => \round(\memory_get_peak_usage(true) / 1024 / 1024, 5),
                'memory_usage_bytes' => $memory,
                'memory_usage_calc_bytes' => \round($memory - $prevMemory, 5),
                'time_sec' => $time,
                'time_calc_sec' => \number_format(\round($time - $prevTime, 5), 5, '.', ''),
            ];
            DebugAnalytics::addData(DebugAnalytics::HL_CHECK, [($message ?: 0) => $data]);
        }
    }

    /** @inheritDoc */
    #[\Override]
    public function isActive(): bool
    {
        return DynamicParams::isDebug();
    }

    /** @inheritDoc */
    #[\Override]
    public static function rollback(): void
    {
        DebugAnalytics::rollback();
    }
}
