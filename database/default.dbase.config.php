<?php
/**
 * This file must be hidden when copying the project, as it contains important information.
 *
 * Этот файл необходимо скрывать при копировании проекта, так как он содержит важную информацию.
 *
 */

define ("HLEB_TYPE_DB", "mysql.myname");

define("HLEB_PARAMETERS_FOR_DB", [

    "mysql.myname" => [
        "mysql:host=localhost",
        "port=3360",
        "dbname=databasename",
        "charset=utf8",
        "user" => "username",
        "pass" => "password"
    ] ,

    "sqlite.myname" => [
        "sqlite:c:/main.db",
        "user" => "username",
        "pass" => "password"
    ] ,

    "postgresql.myname" => [
        "pgsql:host=127.0.0.1",
        "port=5432",
        "dbname=databasename",
        "user" => "username",
        "pass" => "password"
    ] ,

]);