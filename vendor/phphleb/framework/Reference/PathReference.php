<?php

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\FileResourceModificationException;
use Hleb\Main\Insert\ContainerUniqueItem;
use Hleb\Static\Settings;
use RuntimeException;

#[Accessible] #[AvailableAsParent]
class PathReference extends ContainerUniqueItem implements PathInterface, Interface\Path
{
    /** @inheritDoc */
    #[\Override]
    public function relative(string $path): string
    {
        $path = '/' . \trim(str_replace('\\', '/', $path,), '\/');
        $globalPath = '/' . \trim(\str_replace('\\', '/', Settings::getPath('global')), '\/');
        $search = \substr_count($path, $globalPath);
        if (!$search) {
            throw new RuntimeException("The path you change must contain the path to the project's root directory.");
        }
        if ($search > 1) {
            $path = \preg_replace("#^{$globalPath}#", '', $path);
        } else {
            $path = \str_replace($globalPath, '', $path);
        }

        return '@/' . \ltrim($path, '/');
    }

    /** @inheritDoc */
    #[\Override]
    public function createDirectory(string $path, int $permissions = 0775): bool
    {
        $path = \str_replace('\\', '/', $path);
        $parts = \explode('/', $path);
        $file = \end($parts);
        if ($file && \str_contains($file, '.')) {
            \array_pop($parts);
        }
        $dir = \implode('/', $parts);

        if (\str_starts_with($path, '@')) {
            $dir = self::get($path);
        } else {
            $path = self::relative($dir);
        }

        if (!\file_exists($dir)) {
            self::errorSuppression(function() use ($dir, $permissions) {
                \mkdir($dir, $permissions, true);
            });
            \clearstatcache(true, $dir);
        }
        if (!\is_writable($dir)) {
            throw new FileResourceModificationException("The directory `$path` is not available for writing files.");
        }
        if (!\is_dir($dir)) {
            throw new FileResourceModificationException("Directory `$path` was not created");
        }
        return true;
    }

    /** @inheritDoc */
    #[\Override]
    public function exists(string $path): bool
    {
        if (\str_starts_with($path, '@')) {
            return (bool)Settings::getRealPath($path);
        }
        \clearstatcache(true, $path);

        return \file_exists($path);
    }

    /** @inheritDoc */
    #[\Override]
    public function contents(string $path, bool $use_include_path = false, $context = null, int $offset = 0, ?int $length = null): false|string
    {
        if (\str_starts_with($path, '@')) {
            $path = Settings::getPath($path);
        }
        return \file_get_contents($path, $use_include_path, $context, $offset, $length);
    }

    /** @inheritDoc */
    #[\Override]
    public function put(string $path, mixed $data, int $flags = 0, $context = null): false|int
    {
        if (\str_starts_with($path, '@')) {
            $path = Settings::getPath($path);
        }
        \clearstatcache(true, $path);

        return \file_put_contents($path, $data, $flags, $context);
    }

    /** @inheritDoc */
    #[\Override]
    public function isDir(string $path): bool
    {
        if (\str_starts_with($path, '@')) {
            $path = Settings::getPath($path);
        }
        return \is_dir($path);
    }

    /** @inheritDoc */
    #[\Override]
    public function getReal(string $keyOrPath): false|string
    {
        return Settings::getRealPath($keyOrPath);
    }

    /** @inheritDoc */
    #[\Override]
    public function get(string $keyOrPath): false|string
    {
        return Settings::getPath($keyOrPath);
    }

    /**
     * Due to possible concurrent access, some errors need to be suppressed.
     *
     * Из-за возможного конкурентного доступа некоторые ошибки нужно подавлять.
     */
    private static function errorSuppression(callable $callback): void
    {
        try {
            \set_error_handler(function ($_errno, $errstr) {
                throw new RuntimeException($errstr);
            });
            $callback();
        } catch (RuntimeException) {
        } finally {
            \restore_error_handler();
        }
    }
}
