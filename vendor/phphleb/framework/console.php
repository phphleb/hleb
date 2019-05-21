<?php

if (isset($argv[2])) {

    $path = $argv[3] ?? ".";

    switch ($argv[2]) {
        case "--version":
        case "-v":
            print "  Framework HLEB version 1.1.1" . "\n" . "  " . hleb_console_copyright();
            break;
        case "--clear-cache":
        case "-cc":
            array_map('unlink', glob($path .'/storage/cache/routes/*.txt'));
            array_map('unlink', glob($path .'/storage/cache/templates/*.txt'));
            print "  Cache cleared.";
            break;
        case "--help":
        case "-h":
            print " --version or -v" . "\n" . " --clear-cache or -cc" .
                "\n" . " --help or -h" . "\n" . " --routes or -r";
        break;
        case "--routes":
        case "-r":
                print get_routes($path);
            break;
        default:
            print "Missing required arguments after `console`. Add --help to display more options.";
    }
}

function hleb_console_copyright()
{
    $start = "2019";
    $cp = date("Y") != $start ? "$start - " . date("Y") : $start;
    return "(c)$cp Foma Tuturov";
}

function get_routes($path)
{
    $file = $path . "/storage/cache/routes/routes.txt";

    if(file_exists($file)) {

        $routes = json_decode(file_get_contents($file, true), true);

        $title = "\n" . "ROUTE | TYPE | NAME" . "\n" .
            "-----------------------------" . "\n";
        $result = "";
        if (!empty($routes)) {

            foreach ($routes as $route) {
                if (isset($route['data_path'])) {
                    $result .= $route['data_path'] . "  " .
                        strtoupper(implode(", ", $route['type'] ?? [])) .
                        "  " . $route['data_name'] . "\n";
                }
            }
        }
    }
    if (empty($result)) return "No cached routes in project." . "\n";

    return $title . $result;
}

