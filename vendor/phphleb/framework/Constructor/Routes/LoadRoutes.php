<?php

declare(strict_types=1);

namespace Hleb\Constructor\Routes;

class LoadRoutes
{
    private $cache_routes = HLEB_STORAGE_CACHE_ROUTES_DIRECTORY . '/routes.txt';

    private $routes_directory = HLEB_LOAD_ROUTES_DIRECTORY . '/';

    function __construct()
    {

    }

    public function update($data)
    {
        @file_put_contents($this->cache_routes, json_encode($data), LOCK_EX);

        return $data;

    }

    public function load_cache()
    {

        $content = is_writable($this->cache_routes) ? file_get_contents($this->cache_routes) : null;

        if (empty($content)) {

            return false;
        }

        return json_decode($content, true);

    }

    public function comparison()
    {

        if (file_exists($this->cache_routes)) {

            $time = filemtime($this->cache_routes);

            $fileinfos = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->routes_directory)
            );
            foreach ($fileinfos as $pathname => $fileinfo) {
                if (!$fileinfo->isFile()) continue;

                if (filemtime($fileinfo->getRealPath()) > $time) {

                    return false;
                }
            }

            return true;
        }

        return false;


    }


}

