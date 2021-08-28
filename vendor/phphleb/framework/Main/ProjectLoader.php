<?php

declare(strict_types=1);

/*
 * Global coordinator of action execution.
 *
 * Глобальный координатор выполнения действий.
 */

namespace Hleb\Main;

use Hleb\Constructor\Cache\CacheRoutes;
use Hleb\Main\Errors\ErrorOutput;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Main\Insert\PageFinisher;
use Hleb\Constructor\Handlers\{
    ProtectedCSRF, URL, URLHandler
};
use Hleb\Constructor\Workspace;
use Hleb\Constructor\Routes\Route;

final class ProjectLoader extends BaseSingleton
{
    public static function start() {

        $routes_array = (new CacheRoutes())->load();

        $render_map = $routes_array['render'] ?? [];

        if (isset($routes_array['addresses'])) URL::create($routes_array['addresses']);

        $block = (new URLHandler())->page($routes_array);

        unset($routes_array);

        Route::instance()->delete();

        if ($block) {
            if(HLEB_DEFAULT_SESSION_INIT || $_SERVER['REQUEST_METHOD'] !== 'GET') {
                if (!isset($_SESSION)) @session_start();
                if (!isset($_SESSION)) ErrorOutput::get("HL050-ERROR: SESSION not initialized !");
            }
            ProtectedCSRF::testPage($block);
            new Workspace($block, $render_map);
            print PageFinisher::getContent();

        } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            unset($block, $render_map);
            hleb_bt3e3gl60pg8h71e00jep901_error_404();

        } else {
            if (!headers_sent()) {
                http_response_code (404);
            }
        }
    }
}

