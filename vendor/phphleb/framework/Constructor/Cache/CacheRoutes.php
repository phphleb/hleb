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
        $cache = $this->opt->loadCache();
        if (json_encode($cache) !== json_encode($data)) {
            $userAndGroup = $this->getFpmUserName();
            $user = explode(':', $userAndGroup)[0];

            $errors = 'HL021-CACHE_ERROR: No write permission ! ' .
                'Failed to save file to folder `/storage/*`.  You need to change permissions for the web server in this folder. ~ ' .
                'Не удалось сохранить кэш !  Ошибка при записи файла в папку `/storage/*`. Необходимо расширить права веб-сервера для этой папки и вложений. <br>Например, выполнить в терминале ';

            if (!empty($user) && !empty($userAndGroup) && substr_count($userAndGroup, ':') === 1) {
                $errors .= '<span style="color:grey;background-color:#f4f7e4"><code>sudo chown -R ' . $user . ' ./storage</code></span> из корневой директории проекта, здесь <code>' . $userAndGroup . '</code> - это предполагаемый пользователь и группа, под которыми работает веб-сервер.';
            } else {
                $errors .= '<span style="color:grey;background-color:#f4f7e4"><code>sudo chown -R www-data ./storage</code></span> из корневой директории проекта, здесь <code>www-data</code> - это предполагаемый пользователь, под которым работает Apache.';
            }
            ErrorOutput::get($errors);
        }
        return $data;
    }

    // Output and compile the route map.
    // Вывод и компиляция карты роутов.
    private function createRoutes() {
        hl_print_fulfillment_inspector(HLEB_LOAD_ROUTES_DIRECTORY, '/main.php');

        // Reserved name from directory.
        // Используется зарезервированное название директории.
        if (file_exists(HLEB_LOAD_ROUTES_DIRECTORY . '/hlogin/reg.php')) {
            hl_print_fulfillment_inspector(HLEB_LOAD_ROUTES_DIRECTORY, '/hlogin/reg.php');
        }
        Route::instance()->end();
    }

    // Returns the result of trying to determine the username on Linux-like systems.
    // Возвращает результат попытки определения имени пользователя в Linux-подобных системах.
    private function getFpmUserName() {
        return preg_replace('|[\s]+|s', ':', strval(exec('ps -p ' . getmypid() . ' -o user,group')));
    }

}

