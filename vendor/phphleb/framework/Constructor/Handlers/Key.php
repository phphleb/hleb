<?php

declare(strict_types=1);

/*
 * A unique key that initializes this resource.
 *
 * Уникальный ключ, инициализирующий данный ресурс.
 */

namespace Hleb\Constructor\Handlers;

use Hleb\Main\Errors\ErrorOutput;
use Hleb\Main\Insert\BaseSingleton;

final class Key extends BaseSingleton
{
    private static $key = null;

    private static $path = HLEB_GLOBAL_DIRECTORY . '/storage/cache/key/security-key.txt';

    // Creating a secure key.
    // Создаёт защищённый ключ.
    public static function create() {
        if (empty(self::$key)) self::$key = self::set();
    }

    // Returns a secure key.
    // Возвращает защищённый ключ.
    public static function get() {
        if (empty(self::$key)) self::$key = self::set();
        return self::$key;
    }

    // Returns the existing or generated secure key.
    // Возвращает имеющийся или созданный защищённый ключ.
    private static function set() {
        if (isset($_SESSION['_SECURITY_TOKEN'])) {
            return $_SESSION['_SECURITY_TOKEN'];
        }
        if (!file_exists(self::$path)) {
            try {
                $keygen = str_shuffle(md5(strval(random_int(100, 100000))) . bin2hex(random_bytes(30)));
            } catch (\Exception $ex) {
                $keygen = str_shuffle(md5(strval(rand()) . md5(strval(rand()))));
            }
            file_put_contents(self::$path, $keygen, LOCK_EX);
            $_SESSION['_SECURITY_TOKEN'] = $keygen;
            if (!file_exists(self::$path)) {
                ErrorOutput::add("HL028-KEY_ERROR: No write permission '/storage/cache/key/' ! " .
                    "Failed to save file to folder `/storage/*`.  You need to change permissions for the web server in this folder. ~ " .
                    "Не удалось сохранить кэш !  Ошибка при записи файла в папку `/storage/*`. Необходимо расширить права веб-сервера для этой папки и вложений.");
                ErrorOutput::run();
            }
            return $keygen;
        }
        $key = trim(file_get_contents(self::$path));
        $_SESSION['_SECURITY_TOKEN'] = $key;
        return $key;
    }
 }

