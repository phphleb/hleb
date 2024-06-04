<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands\Features\FlatKegling;

use Hleb\Constructor\Data\SystemSettings;
use Hleb\CoreProcessException;
use Hleb\ParseException;
use JsonException;

/**
 * @internal
 */
final class WinStorage
{
    private const CONFIG_FILE = 'config.json';

    private string $configDir;

    public function __construct(private readonly array $position)
    {
        $this->configDir = SystemSettings::getPath('@storage/lib/features/flat-kegling/');
    }

    public function isConfig(): bool
    {
        return \file_exists($this->configDir . self::CONFIG_FILE);
    }

    public function getConfig(): array
    {
        $defaultConfig = $this->defaultConfig();
        if (\file_exists($this->configDir . self::CONFIG_FILE)) {
            try {
                $config = \json_decode(
                    \file_get_contents($this->configDir . self::CONFIG_FILE),
                    true,
                    5,
                    JSON_THROW_ON_ERROR
                );
            } catch (JsonException $e) {
                throw new ParseException($e->getMessage());
            }
            if ($config['time'] > \time() - 3600) {
                return $config;
            }
            $defaultConfig['level'] = $config['level'];
        } else {
            \hl_create_directory($this->configDir);
        }
        return $defaultConfig;
    }

     public function saveConfig(array $data): void
    {
        try {
            @\file_put_contents($this->configDir . self::CONFIG_FILE, \json_encode($data, JSON_THROW_ON_ERROR));
        } catch (\JsonException $e) {
            throw new ParseException($e->getMessage());
        }
        @\chmod($this->configDir . self::CONFIG_FILE, 0664);
    }

    public function defaultConfig(): array
    {
        return [
            'time' => \time(),
            'attempt' => 1,
            'points' => 0,
            'data' => $this->position,
            'level' => 1,
            'frame' => 0,
            'stat' => [],
            'count' => 0,
            'type' => ''
        ];
    }
}
