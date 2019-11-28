<?php

define('HLEB_VENDOR_DIRECTORY', dirname(__DIR__, 2) );

define("HLEB_VENDOR_DIR_NAME", array_reverse(explode(DIRECTORY_SEPARATOR,HLEB_VENDOR_DIRECTORY))[0]);

define('HLEB_PROJECT_DIRECTORY', HLEB_VENDOR_DIRECTORY .'/phphleb/framework');

define('HLEB_PROJECT_DEBUG', false);

define('HLEB_HTTP_TYPE_SUPPORT', ['get', 'post', 'delete', 'put', 'patch', 'options']);

$arguments = $argv[1] ?? null;

$set_arguments = $argv[2] ?? null;

include_once HLEB_PROJECT_DIRECTORY . "/Main/Console/MainConsole.php";

$fn = new \Hleb\Main\Console\MainConsole();

if ($arguments) {

    switch ($arguments) {
        case "--version":
        case "-v":
            $ver = [hl_get_frame_version(), hl_get_framework_version()];
            $bsp = $fn->addBsp($ver);
            print "\n" .
                " ╔═ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ═╗ " . "\n" .
                " ║ " . "HLEB frame". " project version " . $ver[0] . $bsp[0] . "  ║" . "\n" .
                " ║ " . "phphleb/framework" . " version  " . $ver[1] . $bsp[1] . "  ║" . "\n" .
                " ║ " . hl_console_copyright() . "                  ║" . "\n" .
                " ╚═ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ═╝ " . "\n";
            print "\n";
            break;
        case "--clear-cache":
        case "-cc":
            array_map('unlink', glob(HLEB_GLOBAL_DIRECTORY . '/storage/cache/routes/*.txt'));
            print "\n" . "Clearing cache [          ] 0% ";
            $files = glob(HLEB_GLOBAL_DIRECTORY . '/storage/cache/templates/*', GLOB_NOSORT);
            if (count($files)) {
                foreach ($files as $k => $value) {
                    @unlink($value);
                    $fn->progressConsole(count($files), $k);
                }
            } else {
                fwrite(STDOUT, "\r");
                fwrite(STDOUT, "No files in /storage/cache/templates/. Cache cleared.");
            }
            print "\n" . "\n";
            break;
        case "--help":
        case "-h":
            print "\n";
            print " --version or -v" . "\n" . " --clear-cache or -cc" . "\n" . " --info or -i" .
                "\n" . " --help or -h" . "\n" . " --routes or -r" . "\n" . " --list or -l";
            print "\n" . "\n";
            break;
        case "--routes":
        case "-r":
            print $fn->searchNanorouter() . $fn->getRoutes();
            print "\n";
            break;
        case "--list":
        case "-l":
            print $fn->listing();
            print "\n" . "\n";
            break;
        case "--info":
        case "-i":
            $fn->getInfo();
            break;
        default:
            $file = $fn->convertCommandToTask($arguments);

            if (file_exists(HLEB_GLOBAL_DIRECTORY . '/app/Commands/' . $file . ".php")) {

                hl_create_users_task(HLEB_GLOBAL_DIRECTORY, $file, $set_arguments ?? null, HLEB_VENDOR_DIR_NAME ?? null, $fn);

            } else {
                print "Missing required arguments after `console`. Add --help to display more options." . "\n";
            }
    }
} else {
    print "Missing arguments after `console`. Add --help to display more options." . "\n";
}



function hl_console_copyright()
{
    $start = "2019";
    $cp = date("Y") != $start ? "$start - " . date("Y") : $start;
    return "(c)$cp Foma Tuturov";
}

function hl_allowed_http_types($type)
{
    return empty($type) ? "GET" : ((in_array(strtolower($type), HLEB_HTTP_TYPE_SUPPORT)) ? $type : $type . " [NOT SUPPORTED]");
}

function hl_create_users_task($path, $class, $arg, $vendor, $fn)
{
    require HLEB_PROJECT_DIRECTORY . "/Main/Insert/DeterminantStaticUncreated.php";

    require HLEB_PROJECT_DIRECTORY . "/Main/Info.php";

    require HLEB_PROJECT_DIRECTORY . "/Scheme/App/Commands/MainTask.php";

    require HLEB_PROJECT_DIRECTORY . "/Scheme/App/Controllers/MainController.php";

    require HLEB_PROJECT_DIRECTORY . "/Scheme/App/Middleware/MainMiddleware.php";

    require HLEB_PROJECT_DIRECTORY . "/Scheme/App/Models/MainModel.php";

    require HLEB_PROJECT_DIRECTORY . "/Scheme/Home/Main/Connector.php";

    require HLEB_GLOBAL_DIRECTORY  . "/app/Optional/MainConnector.php";

    require HLEB_PROJECT_DIRECTORY . "/Main/MainAutoloader.php";

    require HLEB_PROJECT_DIRECTORY . "/Main/HomeConnector.php";

    // Сторонний автозагрузчик классов

    if (file_exists(HLEB_VENDOR_DIRECTORY . '/autoload.php')) {
        require HLEB_VENDOR_DIRECTORY . '/autoload.php';
    }

    // Собственный автозагрузчик классов

    function hl_main_autoloader($class)
    {
        \Hleb\Main\MainAutoloader::get($class);
    }

    spl_autoload_register('hl_main_autoloader', true, true);

    // Выполнение команды

    $real_path = $path . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . $class . ".php";

    include_once "$real_path";

    $search_names = $fn->searchOnceNamespace($real_path, $path . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Commands');

    if ($search_names) {

        foreach ($search_names as $search_name) {

            if (class_exists('App\Commands\\' . $search_name)) {

                $class_name = 'App\Commands\\' . $search_name;

                (new $class_name())->create_task($arg);

                break;
            }
        }
    }
}


function hl_get_frame_version()
{
    return hl_search_version(HLEB_GLOBAL_DIRECTORY . "/" . HLEB_PUBLIC_DIR . "/index.php", "HLEB_FRAME_VERSION");
}

function hl_get_framework_version()
{
    return hl_search_version(HLEB_PROJECT_DIRECTORY . "/init.php", "HLEB_PROJECT_FULL_VERSION");
}

function hl_search_version($file, $const)
{
    $content = file_get_contents($file, true);

    $search = preg_match_all("|define\(\s*\'" . $const . "\'\s*\,\s*([^\)]+)\)|u", $content, $def);

    return trim($def[1][0] ?? 'undefined', "' \"");
}




