<?php

declare(strict_types=1);

namespace Hleb\Main;

use Hleb\Constructor\Cache\CacheRoutes;
use Hleb\Main\Errors\ErrorOutput;
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

        $render_map = $routes_array['render'] ?? [];

        if (isset($routes_array['addresses'])) URL::create($routes_array['addresses']);

        $block = (new URLHandler())->page($routes_array);

        unset($routes_array);

        Request::close();

        Route::delete();

        if ($block) {

           if(!isset($_SESSION)) @session_start();

           if(!isset($_SESSION)) ErrorOutput::get("HL050-ERROR: SESSION not initialized !");

            ProtectedCSRF::testPage($block);

            new Workspace($block, $render_map);

        } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            unset($block, $render_map);

            include HLEB_GLOBAL_DIRECTORY . '/app/Optional/404.php';

        } else {

            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        }

    }
}

