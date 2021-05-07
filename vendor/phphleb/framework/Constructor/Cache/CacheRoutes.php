<?php

declare(strict_types=1);

/*
 * Caching routemap to file.
 *
 * Кеширование карты маршрутов в файл.
 */

namespace Hleb\Constructor\Cache;

use Hleb\Main\Info;
use Hleb\Constructor\Routes\LoadRoutes;
use Hleb\Constructor\Routes\Route;
use Hleb\Main\Errors\ErrorOutput;

class CacheRoutes
{
    /** @var LoadRoutes|null */
    private $opt = null;

    // Returns an array with data about routes.
    // Возвращает массив с данными о роутах.
    /** @return array */
    public function load() {
        $this->opt = new LoadRoutes();
        if ($this->opt->comparison()) {
            $cache = $this->opt->loadCache();
            if ($cache === false) {
                $this->createRoutes();
                Info::add('CacheRoutes', true);
                return $this->check($this->opt->update(Route::instance()->data()));
            }
            Info::add('CacheRoutes', false);
            return $cache;
        }
        $this->createRoutes();
        Info::add('CacheRoutes', true);
        return $this->check($this->opt->update(Route::instance()->data()));
    }

    // Check the availability of the file with the cache of routes. The contents of the file are returned or an error is displayed.
    // Проверка доступнсти файла с кешем роутов. Возвращается содержимое файла или выводится ошибка.
    private function check($data) {
        if (!is_null($this->opt)) {
            $cache = $this->opt->loadCache();
            if (json_encode($cache) !== json_encode($data)) {
                $userAndGroup = $this->getFpmUserName();
                $user = explode(':', $userAndGroup)[0];

                $errors = 'HL021-CACHE_ERROR: No write permission ! ' .
                    'Failed to save file to folder `/storage/*`.  You need to change the web server permissions in this folder. ~ ' .
                    'Не удалось сохранить кэш !  Ошибка при записи файла в папку `/storage/*`. Необходимо расширить права веб-сервера для этой папки и вложений. <br>Например, выполнить в терминале ';

                if (!empty($user) && !empty($userAndGroup) && substr_count($userAndGroup, ':') === 1) {
                    $errors .= '<span style="color:grey;background-color:#f4f7e4"><code>sudo chown -R ' . $user . ' ./storage</code></span> из корневой директории проекта, здесь <code>' . $userAndGroup . '</code> - это предполагаемый пользователь и группа, под которыми работает веб-сервер.';
                } else {
                    $errors .= '<span style="color:grey;background-color:#f4f7e4"><code>sudo chown -R www-data ./storage</code></span> из корневой директории проекта, здесь <code>www-data</code> - это предполагаемый пользователь, под которым работает Apache.';
                }
                ErrorOutput::get($errors, false);
            }
        }
        return $data;
    }

    // Output and compile the route map.
    // Вывод и компиляция карты роутов.
    private function createRoutes() {
        hleb_require(HLEB_LOAD_ROUTES_DIRECTORY . '/main.php');

        // Reserved file name is used /routes/.../main.php
        // Используется зарезервированное название файла /routes/.../main.php
        $this->addRoutesFromLibs();

        Route::instance()->end();
    }

    // Returns the result of trying to determine the username on Linux-like systems.
    // Возвращает результат попытки определения имени пользователя в Linux-подобных системах.
    private function getFpmUserName() {
        return preg_replace('|[\s]+|s', ':', strval(exec('ps -p ' . getmypid() . ' -o user,group')));
    }

    // Including all main.php files from nested directories.
    // Подключение всех файлов main.php из вложенных директорий.
    private function addRoutesFromLibs() {
        $dir = opendir(HLEB_LOAD_ROUTES_DIRECTORY);
        while ($file = readdir($dir)) {
            $searchFile = DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR . 'main.php';
            if ($file != '.' && $file != '..' && is_dir(HLEB_LOAD_ROUTES_DIRECTORY . DIRECTORY_SEPARATOR . $file) &&
                file_exists(HLEB_LOAD_ROUTES_DIRECTORY . $searchFile)) {
                require_once HLEB_LOAD_ROUTES_DIRECTORY . $searchFile;
            }
        }
    }

}

