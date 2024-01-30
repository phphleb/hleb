<?php

/*declare(strict_types=1);*/

namespace Hleb\Init;

use Hleb\Helpers\NameConverter;
use Hleb\Helpers\StringHelper;
use Hleb\Static\Settings;

/**
 * @internal
 */
final class CustomAutoloader
{
    private static ?NameConverter $converter = null;

    /**
     * Trying to load a class by its name.
     *
     * Попытка загрузить класс по его названию.
     */
    public static function make(string $class, string $globalPath, string $vendorPath): string|false
    {
        $example = \str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        if (\file_exists($globalPath . DIRECTORY_SEPARATOR . \lcfirst($example))) {
            return $globalPath . DIRECTORY_SEPARATOR . \lcfirst($example);
        }
        if (\file_exists($globalPath . DIRECTORY_SEPARATOR . $example)) {
            return $globalPath . DIRECTORY_SEPARATOR . $example;
        }
        self::$converter === null and self::$converter = new NameConverter();

        $examples = \explode('\\', \trim($class, '/\\'));

        if ($examples[0] === \ucfirst(Settings::getParam('system', 'module.namespace'))) {
            return self::searchModule($examples, $globalPath);
        }
        if ($examples[0] === 'Phphleb') {
            return self::searchPsr0($examples, $vendorPath);
        }
        if ($file = self::searchPsr0($examples, $globalPath)) {
            return $file;
        }
        if ($file = self::searchPsr0($examples, $vendorPath)) {
            return $file;
        }
        # :(
        return false;
    }

    /**
     * Separate download in case of module detection.
     *
     * Отдельная загрузка в случае обнаружения модуля.
     */
    private static function searchModule(array $parts, string $globalPath): string|false
    {
        $search = function (array &$parts, int $num): string {
            $parts[$num] = self::$converter->convertClassNameToStr($parts[$num]);
            return \implode(DIRECTORY_SEPARATOR, $parts) . '.php';
        };
        $parts[0] = Settings::getRealPath('modules');
        $parts[1] = self::$converter->convertClassNameToStr($parts[1]);
        for ($i = 2; $i < 7; $i++) {
            if (!isset($parts[$i])) {
                break;
            }
            $file = $search($parts, $i);
            if (\file_exists($file)) {
                return $file;
            }
        }

        return false;
    }

    /**
     * An attempt was made to load a class according to PSR-0.
     *
     * Попытка загрузки класса по соответствию PSR-0.
     */
    private static function searchPsr0(array $examples, string $path): string|false
    {
        $count = \count($examples);
        for ($i = 0; $i < $count; $i++) {
            $ext = ($i + 1) === $count ? '.php' : '';
            $firstVar = $path . DIRECTORY_SEPARATOR . $examples[$i] . $ext;
            if (!file_exists($firstVar)) {
                $firstVar = null;
            }
            $secondVar = $path . DIRECTORY_SEPARATOR . self::$converter->convertClassNameToStr($examples[$i]) . $ext;
            if (!file_exists($secondVar)) {
                $secondVar = null;
            }
            if ($ext) {
                if ($firstVar) {
                    return $firstVar;
                }
                if ($secondVar) {
                    return $secondVar;
                }
            }
            if ($firstVar) {
                $path = $firstVar;
            } else if ($secondVar) {
                $path = $secondVar;
            } else {
                return false;
            }
        }
        return false;
    }
}
