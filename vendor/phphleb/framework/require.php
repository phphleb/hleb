<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////

if (intval(explode('.', phpversion())[0]) < 7)
    die("The application requires PHP version higher than 7.0 (Current version " . phpversion() . ")");

if(empty($_SERVER['REQUEST_METHOD']))
    die('Undefined $_SERVER[\'REQUEST_METHOD\']');

if(empty($_SERVER['HTTP_HOST']))
    die('Undefined $_SERVER[\'HTTP_HOST\']');

///////////////////////////////////////////////////////////////////////////////////////////////////////