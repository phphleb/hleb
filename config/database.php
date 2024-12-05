<?php

if (file_exists(__DIR__ . '/database-local.php')) { return (require __DIR__ . '/database-local.php');}

/*
 * A configuration file to set a default connection and multiple database connections.
 * This file must be hidden when copying the project, as it contains important information.
 * Data can be recursively redefined in modules when creating a similar
 * /modules/{module_name}/config/database.php file.
 * You can pass PDO settings to the `options` parameter.
 *
 * Конфигурационный файл для задания подключения по умолчанию и вариантов подключения к базе данных.
 * Этот файл необходимо скрывать при копировании проекта, так как он содержит важную информацию.
 * Данные можно рекурсивно переопределить в модулях при создании аналогичного файла
 * /modules/{module_name}/config/database.php.
 * В параметр `options` можно передать настройки PDO.
 */
return [
    'base.db.type' => env('DB_TYPE','mysql.name'),

    'mutex.db.type' => env('DB_MUTEX','mysql.name'),
    'redis.db.type' => env('DB_REDIS','redis.name'),

    'db.settings.list' => [

        'mysql.name' => [
            'mysql:host=localhost',
            'port=3306',
            'dbname=%dbname%',
            'charset=utf8',
            'user' => '%username%',
            'pass' => '%password%',
            'options' => [
            // \PDO::ATTR_PERSISTENT => TRUE
            ],
        ],

        'sqlite.name' => [
            'sqlite:c:/main.db',
            'user' => '%username%',
            'pass' => '%password%',
            'options' => [],
        ],

        'postgresql.name' => [
            'pgsql:host=127.0.0.1',
            'port=5432',
            'dbname=%dbname%',
            'user' => '%username%',
            'pass' => '%password%',
            'options' => [],
        ],

        'mysql.sphinx-search' => [
            'mysql:host=127.0.0.1',
            'port=9306',
            'user' => '%username%',
            'pass' => '%password%',
            'options' => [],
        ],

        'redis.name' => [
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => '6379',
         // 'password' => '%password%',
            'options' => [],
        ],

    ]];
