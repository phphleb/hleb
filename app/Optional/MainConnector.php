<?php

/*
 *  Mapping to autoload classes: namespace => realpath
 *
 *  Сопоставление для автозагрузки классов: namespace => realpath
 */

namespace App\Optional;

use Hleb\Scheme\Home\Main\Connector;

class MainConnector implements Connector
{
    public function add() {
        return [
            "App\Controllers\*" => "app/Controllers/",
            "Models\*" => "app/Models/",
            "App\Middleware\Before\*" => "app/Middleware/Before/",
            "App\Middleware\After\*" => "app/Middleware/After/",
            "Modules\*" => "modules/",
            "App\Commands\*" => "app/Commands/",
            // ... or, if a specific class is added,
            // ...или, если добавляется конкретный класс,
            "DB" => "database/DB.php",
            "Phphleb\Debugpan\DPanel" => "vendor/phphleb/debugpan/DPanel.php",
            "XdORM\XD" => "vendor/phphleb/xdorm/XD.php",
            'Phphleb\Adminpan\MainAdminPanel' => 'vendor/phphleb/adminpan/MainAdminPanel.php',
            // ... //
        ];

    }

}

