<?php

declare(strict_types=1);

namespace Hleb\Main;

use Hleb\Scheme\Home\Main\Connector;

class HomeConnector implements Connector
{
    function __construct(){}
    /**
     *  Добавление пути для автозагрузки класса: namespace => realpath
     */
    public function add()
    {
        $vendor = HLEB_VENDOR_DIR_NAME;
        return [
            'Hleb\Constructor\Routes\Methods\RouteMethodBefore'=> $vendor.'/phphleb/framework/Constructor/Routes/Methods/RouteMethodBefore.php',
            'Hleb\Constructor\Routes\MainRouteMethod'=> $vendor.'/phphleb/framework/Constructor/Routes/MainRouteMethod.php',
            'Hleb\Scheme\Home\Constructor\Routes\DataRoute'=> $vendor.'/phphleb/framework/Scheme/Home/Constructor/Routes/DataRoute.php',
            'Hleb\Scheme\Home\Constructor\Routes\RouteMethodStandard'=> $vendor.'/phphleb/framework/Scheme/Home/Constructor/Routes/RouteMethodStandard.php',
            'Hleb\Main\Errors\ErrorOutput'=> $vendor.'/phphleb/framework/Main/Errors/ErrorOutput.php',
            'Hleb\Main\DataDebug'=> $vendor.'/phphleb/framework/Main/DataDebug.php',
            'Hleb\Constructor\Workspace'=> $vendor.'/phphleb/framework/Constructor/Workspace.php',
            'Hleb\Constructor\Cache\CacheRoutes'=> $vendor.'/phphleb/framework/Constructor/Cache/CacheRoutes.php',
            'Hleb\Constructor\Handlers\Key'=> $vendor.'/phphleb/framework/Constructor/Handlers/Key.php',
            'Hleb\Constructor\Handlers\ProtectedCSRF'=> $vendor.'/phphleb/framework/Constructor/Handlers/ProtectedCSRF.php',
            'Hleb\Constructor\Handlers\Request'=> $vendor.'/phphleb/framework/Constructor/Handlers/Request.php',
            'Hleb\Constructor\Handlers\URL'=> $vendor.'/phphleb/framework/Constructor/Handlers/URL.php',
            'Hleb\Constructor\Handlers\URLHandler'=> $vendor.'/phphleb/framework/Constructor/Handlers/URLHandler.php',
            'Hleb\Constructor\Handlers\Head'=> $vendor.'/phphleb/framework/Constructor/Handlers/Head.php',
            'Hleb\Constructor\Handlers\Resources'=> $vendor.'/phphleb/framework/Constructor/Handlers/Resources.php',
            'Hleb\Scheme\Home\Constructor\Handlers\ResourceStandard'=> $vendor.'/phphleb/framework/Scheme/Home/Constructor/Handlers/ResourceStandard.php',
            'Hleb\Constructor\TCreator'=> $vendor.'/phphleb/framework/Constructor/TCreator.php',
            'Hleb\Constructor\TwigCreator'=> $vendor.'/phphleb/framework/Constructor/TwigCreator.php',
            'Hleb\Constructor\VCreator'=> $vendor.'/phphleb/framework/Constructor/VCreator.php',
            'Hleb\Main\MainTemplate'=> $vendor.'/phphleb/framework/Main/MainTemplate.php',
            'MainTask'=> $vendor.'/phphleb/framework/Scheme/App/Commands/MainTask.php',
            'Hleb\Constructor\Cache\CachedTemplate'=> $vendor.'/phphleb/framework/Constructor/Cache/CachedTemplate.php',
            'Hleb\Constructor\Cache\OwnCachedTemplate'=> $vendor.'/phphleb/framework/Constructor/Cache/OwnCachedTemplate.php',
            'Hleb\Scheme\App\Middleware\MainMiddleware'=> $vendor.'/phphleb/framework/Scheme/App/Middleware/MainMiddleware.php',
            'Hleb\Main\WorkDebug'=> $vendor.'/phphleb/framework/Main/WorkDebug.php',
            'Hleb\Main\MyDebug'=>$vendor.'/phphleb/framework/Main/MyDebug.php',
            'Hleb\Constructor\Routes\Methods\RouteMethodPrefix'=> $vendor.'/phphleb/framework/Constructor/Routes/Methods/RouteMethodPrefix.php',
            'Hleb\Constructor\Routes\Methods\RouteMethodGetGroup'=> $vendor.'/phphleb/framework/Constructor/Routes/Methods/RouteMethodGetGroup.php',
            'Hleb\Constructor\Routes\Methods\RouteMethodGetProtect'=> $vendor.'/phphleb/framework/Constructor/Routes/Methods/RouteMethodGetProtect.php',
            'Hleb\Constructor\Routes\Methods\RouteMethodGetType'=> $vendor.'/phphleb/framework/Constructor/Routes/Methods/RouteMethodGetType.php',
            'Hleb\Constructor\Routes\Methods\RouteMethodRenderMap'=> $vendor.'/phphleb/framework/Constructor/Routes/Methods/RouteMethodRenderMap.php',
            'Hleb\Constructor\Routes\Methods\RouteMethodProtect'=> $vendor.'/phphleb/framework/Constructor/Routes/Methods/RouteMethodProtect.php',
            'Hleb\Constructor\Routes\Methods\RouteMethodType'=> $vendor.'/phphleb/framework/Constructor/Routes/Methods/RouteMethodType.php',
            'Hleb\Constructor\Routes\Methods\RouteMethodGet'=> $vendor.'/phphleb/framework/Constructor/Routes/Methods/RouteMethodGet.php',
            'Hleb\Constructor\Routes\Methods\RouteMethodEndGroup'=> $vendor.'/phphleb/framework/Constructor/Routes/Methods/RouteMethodEndGroup.php',
            'Hleb\Constructor\Routes\Methods\RouteMethodName'=> $vendor.'/phphleb/framework/Constructor/Routes/Methods/RouteMethodName.php',
            'Hleb\Constructor\Routes\Methods\RouteMethodController'=> $vendor.'/phphleb/framework/Constructor/Routes/Methods/RouteMethodController.php',
            'Hleb\Constructor\Routes\Methods\RouteMethodWhere'=> $vendor.'/phphleb/framework/Constructor/Routes/Methods/RouteMethodWhere.php',
            'Hleb\Constructor\Routes\Methods\RouteMethodAfter'=> $vendor.'/phphleb/framework/Constructor/Routes/Methods/RouteMethodAfter.php',
            'Hleb\Constructor\Routes\Methods\RouteMethodEndProtect'=> $vendor.'/phphleb/framework/Constructor/Routes/Methods/RouteMethodEndProtect.php',
            'Hleb\Constructor\Routes\Methods\RouteMethodEndType'=> $vendor.'/phphleb/framework/Constructor/Routes/Methods/RouteMethodEndType.php',
            'Hleb\Constructor\Routes\Methods\RouteMethodEnd'=> $vendor.'/phphleb/framework/Constructor/Routes/Methods/RouteMethodEnd.php',
            'Phphleb\Debugpan\DPanel'=>$vendor.'/phphleb/debugpan/DPanel.php',
            'XdORM\Shell\XdHelper'=>$vendor.'/phphleb/xdorm/Shell/XdHelper.php',
            'XdORM\XD'=>$vendor.'/phphleb/xdorm/XD.php',
            'XdORM\Shell\XdDB'=>$vendor.'/phphleb/xdorm/Shell/XdDB.php',
            'Phphleb\Adminpan\MainAdminPanel'=>$vendor.'/phphleb/adminpan/MainAdminPanel.php',
            'Phphleb\Adminpan\Add\AdminPanHandler'=>$vendor.'/phphleb/adminpan/Add/AdminPanHandler.php',
            'Phphleb\Adminpan\Add\GetDataList'=>$vendor.'/phphleb/adminpan/Add/GetDataList.php',
            'Phphleb\Adminpan\Add\GetDataTable'=>$vendor.'/phphleb/adminpan/Add/GetDataTable.php',
            'Phphleb\Adminpan\Add\GetDataHTML'=>$vendor.'/phphleb/adminpan/Add/GetDataHTML.php',
            'Phphleb\Adminpan\Add\GetDataGraph'=>$vendor.'/phphlecomposerb/adminpan/Add/GetDataGraph.php',
            'Hleb\Main\Commands\MainLaunchTask'=>$vendor.'/phphleb/framework/Main/Commands/MainLaunchTask.php',
            'MainController'=>$vendor."/phphleb/framework/Scheme/App/Controllers/MainController.php",
            'MainMiddleware'=>$vendor."/phphleb/framework/Scheme/App/Middleware/MainMiddleware.php",
            'MainModel'=>$vendor."/phphleb/framework/Scheme/App/Models/MainModel.php",
            'Hleb\Constructor\Routes\MainRoute'=>$vendor."/phphleb/framework/Constructor/Routes/MainRoute.php",
            'Route'=>$vendor."/phphleb/framework/Constructor/Routes/Route.php",
            'Hleb\Main\Route\ProjectLoader'=>$vendor."/phphleb/framework/Main/ProjectLoader.php",
            'Hleb\Constructor\Routes\LoadRoutes'=>$vendor."/phphleb/framework/Constructor/Routes/LoadRoutes.php",
            'Hleb\Main\TryClass'=>$vendor."/phphleb/framework/Main/TryClass.php",
        ];
    }
}

