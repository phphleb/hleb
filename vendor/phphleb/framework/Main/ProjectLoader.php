<?php

namespace Hleb\Main;

use Hleb\Constructor\Cache\CacheRoutes;
use Hleb\Constructor\Handlers\{
    ProtectedCSRF, URL, URLHandler, Request
};
use Hleb\Constructor\Workspace;
use \Route;
use DeterminantStaticUncreated;

class ProjectLoader
{
    use DeterminantStaticUncreated;

    public static function start()
    {

        $routes_array = (new CacheRoutes())->load();

        $render_map = $routes_array["render"] ?? [];

        if (isset($routes_array["addresses"])) URL::create($routes_array["addresses"]);


        $block = (new URLHandler())->page($routes_array);

        Request::close();

        Route::delete();

        if ($block) {

            ProtectedCSRF::testPage($block);

            new Workspace($block, $render_map);

        } else if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'GET') {

            include HLEB_GLOBAL_DIRECTORY . "/app/Optional/404.php";

        } else {

            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        }

    }
}