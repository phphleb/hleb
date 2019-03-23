<?php

namespace App\Optional;

use Hleb\Scheme\Home\Main\Connector;

class MainConnector implements Connector
{
    function __construct(){}
    /**
     *  Сопоставление для автозагрузки классов: namespace => realpath
     */
    public function add()
    {

        return [

            "App\Controllers\*" => "app/Controllers/",
            "Models\*" => "app/Models/",
            "App\Middleware\Before\*" => "app/Middleware/Before/",
            "App\Middleware\After\*" => "app/Middleware/After/",
            // ...или, если добавляется конкретный класс,
            "DB" => "database/DB.php",
            "Phphleb\Debugpan\DPanel" => "vendor/phphleb/debugpan/DPanel.php",
            // ... //

        ];

    }

}