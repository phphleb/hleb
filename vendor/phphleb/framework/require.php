<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////

if (intval(explode('.', phpversion())[0]) < 7)
    die("The application requires PHP version higher than 7.0 (Current version " . phpversion() . ")");

if(empty($_SERVER['REQUEST_METHOD']))
    die('Undefined $_SERVER[\'REQUEST_METHOD\']');

if(empty($_SERVER['HTTP_HOST']))
    die('Undefined $_SERVER[\'HTTP_HOST\']');

///////////////////////////////////////////////////////////////////////////////////////////////////////


if (is_dir(dirname(__FILE__, 3) . "/phphleb/radjax/")) {

    $GLOBALS["HLEB_MAIN_DEBUG_RADJAX"] = [];

    if ((file_exists(dirname(__FILE__, 4) . '/routes/ajax.php') ||
        file_exists(dirname(__FILE__, 4) . '/routes/api.php'))
    ){

        require dirname(__DIR__, 2) . "/phphleb/radjax/Route.php";

        require dirname(__DIR__, 2) . "/phphleb/radjax/Src/App.php";

        if (file_exists(dirname(__DIR__, 3) . '/routes/api.php'))
            include_once dirname(__DIR__, 3) . '/routes/api.php';

        if (file_exists(dirname(__DIR__, 3) . '/routes/ajax.php'))
            include_once dirname(__DIR__, 3) . '/routes/ajax.php';

        function radjax_main_autoloader(string $class)
        {
            \Hleb\Main\MainAutoloader::get($class);
        }

        (new Radjax\Src\App(Radjax\Route::getParams()))->get();

    }
}

