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
                    $this->create_routes();
                    Info::add('CacheRoutes', true);
                    return $this->check($this->opt->update(Route::data()));
                }

                Info::add('CacheRoutes', false);
                return $cache;
            }

        $this->create_routes();
        Info::add('CacheRoutes', true);
        return $this->check($this->opt->update(Route::data()));
    }


    private function check($data)
    {
        $cache = $this->opt->load_cache();
        if (json_encode($cache) !== json_encode($data)) {

            $errors = 'HL021-CACHE_ERROR: No write permission ! ' .
                'Failed to save file to folder `/storage/*`.  You need to change permissions on this folder. ~ ' .
                'Не удалось сохранить кэш !  Ошибка при записи файла в папку `/storage/*`. Необходимо расширить права для этой папки и вложений всем пользователям.';

            ErrorOutput::get($errors);
        }

        return $data;
    }


    private function create_routes()
    {
        require HLEB_LOAD_ROUTES_DIRECTORY . '/main.php';
        Route::end();
    }

}

