<?php

declare(strict_types=1);

namespace Hleb\Main;

use Hleb\Main\HomeConnector;
use App\Optional\MainConnector;
use Hleb\Scheme\Home\Main\Connector;

class MainAutoloader
{
    function __construct()
    {
    }

    /**
     * @param $class string
     */
    public static function get(string $class)
    {
        
        if(class_exists($class, false) || interface_exists($class, false) ) return;

        if (self::search_and_include($class, new HomeConnector())) {

            // Проверка внутренних классов

        } else if (self::search_and_include($class, new MainConnector())) {

            // Проверка пользовательских классов

        } else {

            $clarification = '/';

            // Сокращение внутреннего перенаправления

            $path = explode('\\', $class);

            if (count($path) > 1) {

                $path[0] = strtolower($path[0]);

                if($path[0] === 'hleb') {

                    $path[0] = 'phphleb/framework';
                    $clarification = '/' . HLEB_VENDOR_DIR_NAME . '/';
                }

                if($path[0] === 'phphleb') {

                    $clarification = '/' . HLEB_VENDOR_DIR_NAME . '/';
                }

                // По имени библиотеки

                if(isset($path[2])) {

                    // Имя производителя
                    $path_to_vendor_name = HLEB_VENDOR_DIRECTORY . '/' . $path[0];

                    if (is_dir($path_to_vendor_name)) {

                        $clarification = '/' . HLEB_VENDOR_DIR_NAME. '/';

                        if (is_dir($path_to_vendor_name . '/' . strtolower($path[1]))) {

                            $path[1] = strtolower($path[1]);

                        } else {
                            // Составные классы с дефисами в названии файла
                            $hyphenated_name = trim(strtolower(preg_replace('/([A-Z])/', '-$1', $path[1])), '-');

                            if (is_dir( $path_to_vendor_name . "/" . $hyphenated_name)) {

                                $path[1] = $hyphenated_name;
                            }
                        }
                    }
                }

                $class = implode("/", $path);

            }

            // Namespace класса соответствует файловому расположению в проекте


            self::init(HLEB_GLOBAL_DIRECTORY . $clarification . str_replace('\\', "/", $class) . '.php');

        }

    }

    public static function search_and_include(string $class, Connector $connector): bool
    {
        $responding = $connector->add();

        //Если найден класс с прямой ссылкой

        if (isset($responding[$class])) {

            self::init(HLEB_GLOBAL_DIRECTORY . '/' . $responding[$class]);

            return true;

        }

        //Если для класса указано только соответствие папки, в которой искать класс по названию

        foreach ($responding as $key => $value) {
            if (strpos($key, '/*') !== false) {

                $cleared_str = str_replace('*', '', $key);

                if (strpos($cleared_str, $class) === 0) {

                    self::init(HLEB_GLOBAL_DIRECTORY . '/' . $value . $class . '.php');

                    return true;
                }
            }
        }

        return false;
    }


    private static function init(string $path)
    {
        if (is_readable($path) !== false) {

            include_once "$path";
        }
    }


}

