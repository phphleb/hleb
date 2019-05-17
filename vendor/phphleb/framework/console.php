<?php

function hleb_console_copyright(){
    $start = "2019";
    $cp = date("Y") != $start ? "$start - " . date("Y")  : $start;
    return "(c)$cp Foma Tuturov";
}

if(isset($argv[2])){
    $argument = $argv[2];
    switch ($argument) {
        case "--version":
        case "-v":
            print "  Framework HLEB version 1.1.1" . "\n" . "  " . hleb_console_copyright();
            break;
        case "--clear-cache":
        case "-cc":
            array_map('unlink', glob('./storage/cache/routes/*.txt'));
            array_map('unlink', glob('./storage/cache/templates/*.txt'));
            print "  Cache cleared.";
            break;
        case "--help":
        case "-h":
            print " --version or -v" . "\n" . " --clear-cache or -cc" . "\n" . " --help or -h" . "\n";
    }
}

