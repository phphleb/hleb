<?php
/**
 * To check if the framework installation requirements are met, go to http://<domain>/requirements.php
 * From the console execute `php public/requirements.php` (if it is a similar version of PHP with the same settings).
 * After verification, you must delete this file.
 *
 * Для проверки соответствия требований к установке фреймворка, перейдите по адресу http://<domain>/requirements.php
 * Из консоли выполнить `php public/requirements.php` (если это аналогичная версия PHP c одинаковыми настройками).
 * После проверки необходимо удалить этот файл.
 */

/**
 * Folder with the location of the framework libraries.
 *
 * Папка с расположением библиотек фреймворка.
 */
$frameworkDir = dirname(__FILE__) . '/../vendor/phphleb/';

$errors = array();

defined('IS_WEB_') or define('IS_WEB_', empty($argv));

if (!is_dir($frameworkDir . 'framework')) {
    $errors[] = 'The framework directory was not found, if the name of the `vendor` folder is different, correct it in the file path in requirement.php:$frameworkDir.';
}
printErrors($errors);

$minFrameworkVersion = "7.1.0";
$minHloginVersion = "7.4.0";
$isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

$versionInfo = explode('.', phpversion());
if (!version_compare(phpversion(), $minFrameworkVersion, ">=")) {
    $errors[] = "The application requires PHP version equal to or higher than $minFrameworkVersion (Current version " . phpversion() . ")";
}
if (is_dir($frameworkDir  . 'hlogin')) {
    if (!version_compare(phpversion(), $minHloginVersion, ">=")) {
        $errors[] = "The phphleb/hlogin requires PHP version equal to or higher than $minHloginVersion (Current version " . phpversion() . ")";
    }
}
if (IS_WEB_ && empty($_SERVER['REQUEST_METHOD'])) {
    $errors[] = 'Undefined $_SERVER[\'REQUEST_METHOD\']';
}
printErrors($errors);

if (!extension_loaded('pdo')) {
    $errors[] = "There is no `PDO` extension for PHP. You need to install this extension.";
}
if (!extension_loaded('json')) {
    $errors[] = "There is no 'json' extension for PHP. You need to install this extension.";
}
if (is_dir($frameworkDir . 'ucaptcha') || is_dir($frameworkDir . 'hlogin')) {
    if (!extension_loaded('gd') || !function_exists('imagecreatefrompng')) {
        $errors[] = "There is no 'gd' extension for PHP (phphleb/ucaptha). You need to install this extension.";
    }
}
if (is_dir($frameworkDir . 'updater') || is_dir($frameworkDir . 'hlogin')) {
    if (!extension_loaded('readline')) {
        $errors[] = "There is no 'readline' extension for PHP (phphleb/updater). You need to install this extension.";
    }
}
printErrors($errors);

echo "OK" . PHP_EOL;

function printErrors($errors)
{
    if ($errors) {
        $rn = IS_WEB_ ? '<br><br>' : PHP_EOL;
        $label = IS_WEB_ ? "{$rn}&#10060; " : "{$rn}[X] ";
        die((IS_WEB_ ? '<h1>Error</h1>' : 'ERROR') . $label . implode($label, $errors) . $rn);
    }
}