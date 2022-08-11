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

        $routesArray = (new CacheRoutes())->load();

        $renderMap = $routesArray['render'] ?? [];

        if (isset($routesArray['addresses'])) URL::create($routesArray['addresses']);

        $block = (new URLHandler())->page($routesArray);

        unset($routesArray);

        Route::instance()->clear();

        if ($block) {
            if(HLEB_DEFAULT_SESSION_INIT || $_SERVER['REQUEST_METHOD'] !== 'GET') {
                if (!isset($_SESSION)) @session_start();
                if (!isset($_SESSION)) ErrorOutput::get("HL050-ERROR: SESSION not initialized !");
            }
            ProtectedCSRF::testPage($block);

            new Workspace($block, $renderMap);

            print PageFinisher::getContent();

        } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            unset($block, $renderMap);
            hleb_page_404();

        } else {
            if (!headers_sent()) {
                http_response_code (404);
            }
        }
    }
}

