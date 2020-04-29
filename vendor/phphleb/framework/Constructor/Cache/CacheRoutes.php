<?php

declare(strict_types=1);

namespace Hleb\Constructor\Cache;

use Hleb\Main\Info;
use Hleb\Constructor\Routes\LoadRoutes;
use Route;
use Hleb\Main\Errors\ErrorOutput;

class CacheRoutes
{
    /**
     * @var null|LoadRoutes
     */
    private $opt = null;
    /**
     * @return array
     */
    public function load()
    {
        $this->opt = new LoadRoutes();

            if ($this->opt->comparison()) {
                $cache = $this->opt->load_cache();

                if ($cache === false) {
                    $this->createRoutes();
                    Info::add('CacheRoutes', true);
                    return $this->check($this->opt->update(Route::data()));
                }

                Info::add('CacheRoutes', false);
                return $cache;
            }

        $this->createRoutes();
        Info::add('CacheRoutes', true);
        return $this->check($this->opt->update(Route::data()));
    }

    private function check($data)
    {
        $cache = $this->opt->load_cache();
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

    private function createRoutes()
    {
        require HLEB_LOAD_ROUTES_DIRECTORY . '/main.php';
        Route::end();
    }

    private function getFpmUserName()
    {
        return preg_replace('|[\s]+|s', ':', strval(exec('ps -p ' . getmypid() . ' -o user,group')));
    }

}

