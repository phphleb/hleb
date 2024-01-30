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
 * The name of the project libraries folder.
 * You need to change this value if the folder name is different.
 *
 * Название папки с библиотеками проекта.
 * Нужно изменить это значение, если папка называется по-другому.
 */
$libraryDirName = 'vendor';

/**
 * Folder with the location of the framework libraries.
 *
 * Папка с расположением библиотек фреймворка.
 */
$frameworkDir = dirname(__FILE__) . "/../$libraryDirName/phphleb/";

$errors = array();
$info = array();

defined('IS_WEB_') or define('IS_WEB_', empty($argv));

if (!@is_dir($frameworkDir . 'framework')) {
    $errors[] = "The framework directory was not found, if the name of the `$libraryDirName` folder is different, correct it in the file path in requirement.php.";
}

$minPhpVersion = "8.2";
$isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
$versionInfo = explode('.', phpversion());
$phpVersion = $versionInfo[0] . '.' . $versionInfo[1];

$install = 'You need to install this extension.';

if ($minPhpVersion > $phpVersion) {
    $errors[] = "The application requires PHP version equal to or higher than $minPhpVersion (Current version " . phpversion() . ")";
}
if (IS_WEB_ && empty($_SERVER['REQUEST_METHOD'])) {
    $errors[] = 'Undefined $_SERVER[\'REQUEST_METHOD\']';
}

if (!$errors) {
    if (!extension_loaded('pdo')) {
        $errors[] = "There is no `PDO` extension for PHP. $install";
    }
    if (!extension_loaded('json')) {
        $errors[] = "There is no 'json' extension for PHP. $install";
    }
    if (@is_dir($frameworkDir . 'ucaptcha')) {
        if (!extension_loaded('gd') || !function_exists('imagecreatefrompng')) {
            $errors[] = "There is no 'gd' extension for PHP (phphleb/ucaptha). $install";
        }
    }
    if (@is_dir($frameworkDir . 'updater')) {
        if (!extension_loaded('readline')) {
            $info[] = "There is no 'readline' extension for PHP (phphleb/updater). The extension may not be supported for hosting.";
        }
    }
    if (@is_dir($frameworkDir . 'hlogin')) {
        if (!extension_loaded('mbstring')) {
            $errors[] = "There is no 'mbstring' extension for PHP (phphleb/hlogin). $install";
        }
    }
}

$rn = IS_WEB_ ? '<br><br>' : "\n";
if ($errors) {
    $label = IS_WEB_ ? "{$rn}&#10060; " : "{$rn}[X] ";
    echo((IS_WEB_ ? '<h1>Error</h1>' : 'ERROR') . $label . implode($label, $errors) . $rn);
}
if ($info) {
    $label = IS_WEB_ ? "{$rn}&#9679; " : "{$rn}[i] ";
    echo((IS_WEB_ ? '<h1>Notification</h1>' : 'NOTIFICATION') . $label . implode($label, $info) . $rn);
}
if (!$errors) {
    echo "OK" . $rn;
}
