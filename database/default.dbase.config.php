<?php
/*
 * A configuration file to set a default connection and multiple database connections.
 * This file must be hidden when copying the project, as it contains important information.
 *
 * Конфигурационный файл для задания подключения по умолчанию и нескольких вариантов подключений к базе данных.
 * Этот файл необходимо скрывать при копировании проекта, так как он содержит важную информацию.
 */

define('HLEB_TYPE_DB', 'mysql.name');

define('HLEB_TYPE_REDIS', 'redis.name');

define('HLEB_PARAMETERS_FOR_DB', [

    'mysql.name' => [
        'mysql:host=localhost',
        'port=3306',
        'dbname=databasename',
        'charset=utf8',
        'user' => 'username',
        'pass' => 'password'
    ],

    'sqlite.name' => [
        'sqlite:c:/main.db',
        'user' => 'username',
        'pass' => 'password'
    ],

    'postgresql.name' => [
        'pgsql:host=127.0.0.1',
        'port=5432',
        'dbname=databasename',
        'user' => 'username',
        'pass' => 'password'
    ],

    'mysql.sphinx-search' => [
        'mysql:host=127.0.0.1',
        'port=9306',
        'user' => 'username',
        'pass' => 'password'
    ],

    'redis.name' => [
        'scheme' => 'tcp',
        'host' => '127.0.0.1',
        'port' => '6379',
    /** 'password' => 'password' */
    ]

]);

