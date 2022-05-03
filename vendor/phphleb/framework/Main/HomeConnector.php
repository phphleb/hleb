<?php

declare(strict_types=1);

/*
 * Adding a path for class autoloading: namespace => realpath.
 *
 * Добавление пути для автозагрузки класса: namespace => realpath.
 */

namespace Hleb\Main;

use Hleb\Scheme\Home\Main\Connector;

final class HomeConnector implements Connector
{
    public function __construct(){}

    const ALL = [
        'Hleb\Constructor\Routes\Methods\RouteMethodBefore' => '/phphleb/framework/Constructor/Routes/Methods/RouteMethodBefore.php',
        'Hleb\Constructor\Routes\MainRouteMethod' => '/phphleb/framework/Constructor/Routes/MainRouteMethod.php',
        'Hleb\Scheme\Home\Constructor\Routes\DataRoute' => '/phphleb/framework/Scheme/Home/Constructor/Routes/DataRoute.php',
        'Hleb\Scheme\Home\Constructor\Routes\RouteMethodStandard' => '/phphleb/framework/Scheme/Home/Constructor/Routes/RouteMethodStandard.php',
        'Hleb\Main\Errors\ErrorOutput' => '/phphleb/framework/Main/Errors/ErrorOutput.php',
        'Hleb\Main\DataDebug' => '/phphleb/framework/Main/DataDebug.php',
        'Hleb\Constructor\Workspace' => '/phphleb/framework/Constructor/Workspace.php',
        'Hleb\Constructor\Cache\CacheRoutes' => '/phphleb/framework/Constructor/Cache/CacheRoutes.php',
        'Hleb\Constructor\Handlers\Key' => '/phphleb/framework/Constructor/Handlers/Key.php',
        'Hleb\Constructor\Handlers\ProtectedCSRF' => '/phphleb/framework/Constructor/Handlers/ProtectedCSRF.php',
        'Hleb\Scheme\Home\Constructor\Handlers\RequestInterface' => "/phphleb/framework/Scheme/Home/Constructor/Handlers/RequestInterface.php",
        'Hleb\Constructor\Handlers\Request' => '/phphleb/framework/Constructor/Handlers/Request.php',
        'Hleb\Constructor\Handlers\URL' => '/phphleb/framework/Constructor/Handlers/URL.php',
        'Hleb\Constructor\Handlers\URLHandler' => '/phphleb/framework/Constructor/Handlers/URLHandler.php',
        'Hleb\Constructor\Handlers\Head' => '/phphleb/framework/Constructor/Handlers/Head.php',
        'Hleb\Constructor\Handlers\Resources' => '/phphleb/framework/Constructor/Handlers/Resources.php',
        'Hleb\Scheme\Home\Constructor\Handlers\ResourceStandard' => '/phphleb/framework/Scheme/Home/Constructor/Handlers/ResourceStandard.php',
        'Hleb\Scheme\Home\Constructor\Handlers\HeadInterface' => '/phphleb/framework/Scheme/Home/Constructor/Handlers/HeadInterface.php',
        'Hleb\Constructor\TCreator' => '/phphleb/framework/Constructor/TCreator.php',
        'Hleb\Constructor\TwigCreator' => '/phphleb/framework/Constructor/TwigCreator.php',
        'Hleb\Constructor\VCreator' => '/phphleb/framework/Constructor/VCreator.php',
        'Hleb\Main\MainTemplate' => '/phphleb/framework/Main/MainTemplate.php',
        'Hleb\Scheme\App\Commands\MainTask' => '/phphleb/framework/Scheme/App/Commands/MainTask.php',
        'Hleb\Constructor\Cache\CachedTemplate' => '/phphleb/framework/Constructor/Cache/CachedTemplate.php',
        'Hleb\Constructor\Cache\OwnCachedTemplate' => '/phphleb/framework/Constructor/Cache/OwnCachedTemplate.php',
        'Hleb\Scheme\App\Middleware\MainMiddleware' => '/phphleb/framework/Scheme/App/Middleware/MainMiddleware.php',
        'Hleb\Main\WorkDebug' => '/phphleb/framework/Main/WorkDebug.php',
        'Hleb\Main\MyDebug' => '/phphleb/framework/Main/MyDebug.php',
        'Hleb\Main\Insert\PageFinisher' => '/phphleb/framework/Main/Insert/PageFinisher.php',
        'Hleb\Constructor\Routes\Methods\RouteMethodPrefix' => '/phphleb/framework/Constructor/Routes/Methods/RouteMethodPrefix.php',
        'Hleb\Constructor\Routes\Methods\RouteMethodGetGroup' => '/phphleb/framework/Constructor/Routes/Methods/RouteMethodGetGroup.php',
        'Hleb\Constructor\Routes\Methods\RouteMethodGetProtect' => '/phphleb/framework/Constructor/Routes/Methods/RouteMethodGetProtect.php',
        'Hleb\Constructor\Routes\Methods\RouteMethodGetType' => '/phphleb/framework/Constructor/Routes/Methods/RouteMethodGetType.php',
        'Hleb\Constructor\Routes\Methods\RouteMethodRenderMap' => '/phphleb/framework/Constructor/Routes/Methods/RouteMethodRenderMap.php',
        'Hleb\Constructor\Routes\Methods\RouteMethodProtect' => '/phphleb/framework/Constructor/Routes/Methods/RouteMethodProtect.php',
        'Hleb\Constructor\Routes\Methods\RouteMethodType' => '/phphleb/framework/Constructor/Routes/Methods/RouteMethodType.php',
        'Hleb\Constructor\Routes\Methods\RouteMethodGet' => '/phphleb/framework/Constructor/Routes/Methods/RouteMethodGet.php',
        'Hleb\Constructor\Routes\Methods\RouteMethodEndGroup' => '/phphleb/framework/Constructor/Routes/Methods/RouteMethodEndGroup.php',
        'Hleb\Constructor\Routes\Methods\RouteMethodName' => '/phphleb/framework/Constructor/Routes/Methods/RouteMethodName.php',
        'Hleb\Constructor\Routes\Methods\RouteMethodController' => '/phphleb/framework/Constructor/Routes/Methods/RouteMethodController.php',
        'Hleb\Constructor\Routes\Methods\RouteMethodWhere' => '/phphleb/framework/Constructor/Routes/Methods/RouteMethodWhere.php',
        'Hleb\Constructor\Routes\Methods\RouteMethodAfter' => '/phphleb/framework/Constructor/Routes/Methods/RouteMethodAfter.php',
        'Hleb\Constructor\Routes\Methods\RouteMethodEndProtect' => '/phphleb/framework/Constructor/Routes/Methods/RouteMethodEndProtect.php',
        'Hleb\Constructor\Routes\Methods\RouteMethodEndType' => '/phphleb/framework/Constructor/Routes/Methods/RouteMethodEndType.php',
        'Hleb\Constructor\Routes\Methods\RouteMethodEnd' => '/phphleb/framework/Constructor/Routes/Methods/RouteMethodEnd.php',
        'Phphleb\Debugpan\DPanel' => '/phphleb/debugpan/DPanel.php',
        'XdORM\Shell\XdHelper' => '/phphleb/xdorm/Shell/XdHelper.php',
        'XdORM\XD' => '/phphleb/xdorm/XD.php',
        'Hleb\Main\PdoManager' => '/phphleb/framework/Main/PdoManager.php',
        'Hleb\Main\DB' => '/phphleb/framework/Main/DB.php',
        'Hleb\Main\MainDB' => '/phphleb/framework/Main/MainDB.php',
        'XdORM\Shell\XdDB' => '/phphleb/xdorm/Shell/XdDB.php',
        'Radjax\Src\RCreator' => '/phphleb/radjax/Src/RCreator.php',
        'Phphleb\Adminpan\MainAdminPanel' => '/phphleb/adminpan/MainAdminPanel.php',
        'Phphleb\Adminpan\Add\AdminPanHandler' => '/phphleb/adminpan/Add/AdminPanHandler.php',
        'Phphleb\Adminpan\Add\GetDataList' => '/phphleb/adminpan/Add/GetDataList.php',
        'Phphleb\Adminpan\Add\GetDataTable' => '/phphleb/adminpan/Add/GetDataTable.php',
        'Phphleb\Adminpan\Add\GetDataHTML' => '/phphleb/adminpan/Add/GetDataHTML.php',
        'Phphleb\Adminpan\Add\GetDataGraph' => '/phphlecomposerb/adminpan/Add/GetDataGraph.php',
        'Hleb\Main\Commands\MainLaunchTask' => '/phphleb/framework/Main/Commands/MainLaunchTask.php',
        'Hleb\Scheme\App\Controllers\MainController' => '/phphleb/framework/Scheme/App/Controllers/MainController.php',
        'Hleb\Scheme\App\Models\MainModel' => '/phphleb/framework/Scheme/App/Models/MainModel.php',
        'Hleb\Constructor\Routes\MainRoute' => '/phphleb/framework/Constructor/Routes/MainRoute.php',
        'Hleb\Constructor\Routes\Route' => '/phphleb/framework/Constructor/Routes/Route.php',
        'Hleb\Main\Route\ProjectLoader' => '/phphleb/framework/Main/ProjectLoader.php',
        'Hleb\Constructor\Routes\LoadRoutes' => '/phphleb/framework/Constructor/Routes/LoadRoutes.php',
        'Hleb\Main\TryClass' => '/phphleb/framework/Main/TryClass.php',
        'Phphleb\Updater\FileRemover' => '/phphleb/updater/FileRemover.php',
        'Phphleb\Updater\FileUploader' => '/phphleb/updater/FileUploader.php',
        'Hleb\Main\Helpers\RangeChecker' => '/phphleb/framework/Main/Helpers/RangeChecker.php',
        'Hleb\Main\Logger\FileLogger'  =>'/phphleb/framework/Main/Logger/FileLogger.php',
        'Hleb\Main\Logger\LogLevel'  => '/phphleb/framework/Main/Logger/LogLevel.php',
        'Hleb\Main\Logger\Log'  => '/phphleb/framework/Main/Logger/Log.php',
        'Hleb\Main\MirrorDB' => '/phphleb/framework/Main/MirrorDB.php',
        'Hleb\Main\App' => '/phphleb/framework/Main/App.php',
        'Hleb\Constructor\Handlers\MirrorRequest' => '/phphleb/framework/Constructor/Handlers/MirrorRequest.php',
        'Hleb\Scheme\Home\Main\LoggerInterface' => '/phphleb/framework/Scheme/Home/Main/LoggerInterface.php'
    ];

    public function add() {
        return self::ALL;
    }
}

