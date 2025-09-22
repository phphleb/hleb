<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Data;

use Hleb\CoreProcessException;
use Hleb\Helpers\Abracadabra;
use Hleb\Main\Insert\BaseSingleton;

final class Key extends BaseSingleton
{
    private static ?string $key = null;

    private static string $path = 'storage/keys/project-security.key';

    private static string $name = 'HLEB_SECURITY_TOKEN';

    /**
     * Returns the secret identifier of the current project on the HLEB framework.
     *
     * Возвращает секретный идентификатор текущего проекта на фреймворке HLEB.
     */
    public static function get(): string
    {
        if (self::$key !== null) {
            return self::$key;
        }
        if (isset($_SESSION[self::$name]) && $_SESSION[self::$name]) {
            return $_SESSION[self::$name];
        }
        $path = SystemSettings::getRealPath('@' . self::$path);
        if ($path) {
            self::$key = \file_get_contents($path);
        }
        if (!self::$key) {
            $path = SystemSettings::getRealPath('global') . '/' . self::$path;
            self::$key = Abracadabra::generate();
            \hl_create_directory($path);
            \file_put_contents($path, self::$key, LOCK_EX);
            @\chmod($path, 0664);

            if (!SystemSettings::getRealPath('@' . self::$path)) {
                throw new CoreProcessException('Failed to save key to folder `/storage/*`. You need to change permissions for the web server in this folder.');
            }
        }

        return $_SESSION[self::$name] = self::$key;
    }

    /**
     * Generates a key if it has not been created previously.
     *
     * Генерирует ключ если он не был создан ранее.
     */
    public static function generateIfNotExists(): bool
    {
        return !empty(self::get());
    }
}
