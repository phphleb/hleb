<?php

namespace Hleb\Constructor\Routes;

class LoadRoutes
{
    private $cache_routes = HLEB_GLOBAL_DIRECTORY . "/storage/cache/routes/routes.txt";

    private $routes_directory = HLEB_GLOBAL_DIRECTORY . "/routes/";

    function __construct()
    {

    }

    public function update($data)
    {
        file_put_contents($this->cache_routes, json_encode($data), LOCK_EX);

        return $data;

    }

    public function load_cache()
    {

        $content = file_get_contents($this->cache_routes);

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