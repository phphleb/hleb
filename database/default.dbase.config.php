<?php
/**
 * This file must be hidden when copying the project, as it contains important information.
 *
 * Этот файл необходимо скрывать при копировании проекта, так как он содержит важную информацию.
 *
 */

define ("HLEB_TYPE_DB", "mysql"); // mysql / sqlite / postgresql

define("HLEB_PARAMETERS_FOR_DB", [

   "mysql" => [
       "DB_HOST" => "localhost",
       "DB_PORT" =>  3360,
       "DB_NAME" => "dbname",
       "DB_CHAR" => "utf8",
       "DB_USER" => "username",
       "DB_PASS" => "password"
   ] ,

    "sqlite" => [
        "DB_PATH" => "c:/main.db",
        "DB_USER" => "username",
        "DB_PASS" => "password"
    ] ,

    "postgresql" => [
        "DB_HOST" => "127.0.0.1",
        "DB_PORT" =>  5432,
        "DB_NAME" => "dbname",
        "DB_USER" => "username",
        "DB_PASS" => "password"
    ] ,

]);