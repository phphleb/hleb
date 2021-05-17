<?php
/*
 * A configuration file to set a default connection and multiple database connections.
 * This file must be hidden when copying the project, as it contains important information.
 *
 * Конфигурационный файл для задания подключения по умолчанию и нескольких вариантов подключений к базе данных.
 * Этот файл необходимо скрывать при копировании проекта, так как он содержит важную информацию.
 */

define("HLEB_TYPE_DB", "mysql.myname");

define("HLEB_PARAMETERS_FOR_DB", [

    "mysql.myname" => [
        "mysql:host=localhost",
        "port=3360",
        "dbname=databasename",
        "charset=utf8",
        "user" => "username",
        "pass" => "password"
    ],

    "sqlite.myname" => [
        "sqlite:c:/main.db",
        "user" => "username",
        "pass" => "password"
    ],

    "postgresql.myname" => [
        "pgsql:host=127.0.0.1",
        "port=5432",
        "dbname=databasename",
        "user" => "username",
        "pass" => "password"
    ],

    "mysql.sphinx-search" => [
        "mysql:host=127.0.0.1",
        "port=9306",
        "user" => "username",
        "pass" => "password"
    ],

]);

