<?php

declare(strict_types=1);

namespace Hleb\Constructor\Handlers;

use DeterminantStaticUncreated;
use Hleb\Main\Errors\ErrorOutput;

class Key
{
    use DeterminantStaticUncreated;

    private static $key = null;

    private static $path = HLEB_GLOBAL_DIRECTORY . '/storage/cache/key/security-key.txt';

    public static function create()
    {

        if (empty(self::$key)) self::$key = self::set();
    }

    public static function get()
    {
        if (empty(self::$key)) self::$key = self::set();

        return self::$key;
    }

    private static function set()
    {

        if (isset($_SESSION['_SECURITY_TOKEN'])) {

            return $_SESSION['_SECURITY_TOKEN'];

        }

        try {

            $randstr = bin2hex(random_bytes(30));

            $keygen = str_shuffle(md5(strval(random_int(100, 100000))) . $randstr);

        } catch (\Exception $ex) {

            $keygen = str_shuffle(md5(strval(rand())));
        }


        if (!file_exists(self::$path)) {

            file_put_contents(self::$path, $keygen, LOCK_EX);

            $_SESSION['_SECURITY_TOKEN'] = $keygen;

            if (!file_exists(self::$path)) {

                ErrorOutput::add("HL028-KEY_ERROR: No write permission '/storage/cashe/key/' ! " .
                    "Failed to save file to folder `/storage/*`.  You need to change permissions on this folder. ~ " .
                    "Не удалось сохранить кэш !  Ошибка при записи файла в папку `/storage/*`. Необходимо расширить права для этой папки и вложений всем пользователям.");

                ErrorOutput::run();

            }

            return $keygen;

        }

        $key = trim(file_get_contents(self::$path));

        if (empty($key)) {

            $key = $keygen;
        }

        $_SESSION['_SECURITY_TOKEN'] = $key;

        return $key;
    }

}

