<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands\Features\AutoloaderSupport;

use FilesystemIterator;
use Hleb\Helpers\ClassDataInFile;
use Hleb\Init\Connectors\HlebConnector;
use Hleb\Main\Console\Commands\Features\FeatureInterface;
use Hleb\Static\Settings;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Control of classes assigned in the primary autoloader.
 *
 * Контроль классов, назначенных в первичном автозагрузчике.
 *
 * @internal
 */
final class AutoloaderSupport implements FeatureInterface
{
    private const DESCRIPTION = 'Checking the core classes of the framework for presence in the autoloader.';

    private const EXCLUDED = [
        'Hleb\Init\Connectors\HlebConnector',
        'Hleb\Main\Logger\LoggerAdapter',
    ];

    private int $code = 0;

    /**
     * Returns the execution time of an empty console request.
     *
     * Возвращает названия классов не предусмотренные в автозагрузчике фреймворка.
     */
    #[\Override]
    public function run(array $argv): string
    {
        $result = '';
        $all = [];
        $dir = Settings::getRealPath('@framework');
        $classes = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($classes as $class) {
            $helper = new ClassDataInFile($class->getRealPath());
            if ($helper->isClass() && !str_contains($helper->getClass(), '_')) {
                $error = $this->check($class->getRealPath(), $helper->getClass());
                $error and $result .= $error;
                $all[] = $helper->getClass();
            }
        }
        if ($result) {
            $result = 'Not found in autoloader:' . PHP_EOL . $result;
        }
        $excess = $this->checkExcess($all);
        if ($excess) {
            $result .= PHP_EOL . 'Extra data in the autoloader:' . PHP_EOL . $excess;
        }

        $this->code = $result ? 1 : 0;

        return $result ?: 'OK' . PHP_EOL;
    }

    /** @inheritDoc */
    #[\Override]
    public static function getDescription(): string
    {
        return self::DESCRIPTION;
    }

    /** @inheritDoc */
    #[\Override]
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Checking for the existence of a class.
     *
     * Проверка на существование класса.
     */
    private function check(string $path, string $class): string|null
    {
        $map = $this->getMap();
        if (!\array_key_exists($class, $map)) {
            if (!\in_array($class, self::EXCLUDED)) {
                return '[class] ' . $class . PHP_EOL;
            }
        } else {
            $p = \str_replace('\\', '/', $path);
            $cl = \str_replace('\\', '/', $map[$class]);
            if (!\str_ends_with($p, \ltrim($cl, '/\\'))) {
                return '[file] ' . $class . PHP_EOL;
            }
        }
        return null;
    }

    /**
     * Checking for non-existent classes.
     *
     * Проверка на наличие не существующих классов.
     */
    private function checkExcess(array $classes): string
    {
        $result = '';
        foreach ($this->getMap() as $name => $file) {
            if (!\in_array($name, $classes)) {
                $result .= '[none] ' . $name . PHP_EOL;
            }
        }
        return $result;
    }

    private function getMap(): array
    {
        return \array_merge(
            HlebConnector::$map,
            HlebConnector::$exceptionMap,
            HlebConnector::$formattedMap,
        );
    }
}
