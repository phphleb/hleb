<?php

declare(strict_types=1);

namespace Hleb\Main;

use App\Optional\MainConnector;
use Hleb\Scheme\Home\Main\Connector;

final class MainAutoloader
{
    public static function get(string $class) {
        if (class_exists($class, false) || interface_exists($class, false)) return;
        if (self::search_and_include($class, new HomeConnector())) {
            /* Checking inner classes. */
            /* Проверка внутренних классов. */
        } else if (self::search_and_include($class, new MainConnector())) {
            /* Checking custom classes. */
            /* Проверка пользовательских классов. */
        } else {
            $clarification = '/';
            /* Reduce internal redirection. */
            /* Сокращение внутреннего перенаправления */
            $path = explode('\\', $class);
            if (count($path) > 1) {
                $path[0] = strtolower($path[0]);
                if ($path[0] === 'hleb') {
                    $path[0] = 'phphleb/framework';
                    $clarification = '/' . HLEB_VENDOR_DIR_NAME . '/';
                } elseif ($path[0] === 'phphleb') {
                    $clarification = '/' . HLEB_VENDOR_DIR_NAME . '/';
                }
                /* By the name of the library. */
                /* По имени библиотеки. */
                if (isset($path[2])) {
                    $pathToVendorName = HLEB_VENDOR_DIRECTORY . '/' . $path[0];
                    if (is_dir($pathToVendorName)) {
                        $clarification = '/' . HLEB_VENDOR_DIR_NAME . '/';
                        if (is_dir($pathToVendorName . '/' . strtolower($path[1]))) {
                            $path[1] = strtolower($path[1]);
                        } else {
                            /* Compound classes with hyphens in the file name. */
                            /* Составные классы с дефисами в названии файла.  */
                            $hyphenatedName = trim(strtolower(preg_replace('/([A-Z])/', '-$1', $path[1])), '-');
                            if (is_dir($pathToVendorName . "/" . $hyphenatedName)) {
                                $path[1] = $hyphenatedName;
                            }
                        }
                    }
                }
                $class = implode("/", $path);
            }
            /* The class namespace corresponds to the file location in the project. */
            /* Namespace класса соответствует файловому расположению в проекте. */
            self::init(HLEB_GLOBAL_DIRECTORY . $clarification . str_replace('\\', "/", $class) . '.php');
        }

    }

    public static function search_and_include(string $class, Connector $connector): bool {
        $responding = $connector->add();
        /* If a class with a direct link is found. */
        /* Если найден класс с прямой ссылкой. */
        if (isset($responding[$class])) {
            self::init(HLEB_GLOBAL_DIRECTORY . '/' . $responding[$class]);
            return true;
        }

        /* If only the correspondence of the folder. */
        /* Если указано только соответствие папки. */
        foreach ($responding as $key => $value) {
            if (strpos($key, '\\*') !== false) {
                $clearedStr = str_replace('\*', '', $key);
                if (strpos($class, $clearedStr) === 0) {
                    $searchFileName = str_replace("\\", "/", '/' . $class . '.php');
                    if (file_exists(HLEB_GLOBAL_DIRECTORY . $searchFileName)) {
                        self::init(HLEB_GLOBAL_DIRECTORY . $searchFileName, false);
                        return true;
                    } else {
                        $fileParts = explode("/", $searchFileName);
                        foreach ($fileParts as $keyPart => &$part) {
                            if (strlen($part)) {
                                $part = strtolower($part);
                                $searchFile = HLEB_GLOBAL_DIRECTORY . implode(DIRECTORY_SEPARATOR, $fileParts);
                                if (file_exists($searchFile)) {
                                    self::init($searchFile, false);
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    private static function init(string $path, $test = true) {
        if ($test && is_readable($path) === false) return;
        include_once "$path";
    }
}

