<?php

declare(strict_types=1);

/*
 * A wrapper for working with PDO. This class is required for compatibility with old versions, where inheritance came from it.
 *
 * Оболочка для работы с PDO. Этот класс необходим для совместимости со старыми версиями, где от него происходило наследование.
 */

namespace Hleb\Main;

class DB
{
    /*
     |--------------------------------------------------------------------------------------
     | Examples of appeals.
     |--------------------------------------------------------------------------------------
     | \DB::run() -  secure connection to the database of the form
     | ("SELECT name_ru,icon,link_ru FROM `catalogs` WHERE `show`=? AND `type`=?", $args)
     | where $args is an enumeration of the values (show and type) in the array.
     |
     | # Getting one line.
     | $id  = 1;
     | $row = DB::run("SELECT * FROM tablename WHERE id=?", [$id])->fetch();
     | (returns the array)
     |
     | # Getting one value.
     | $type = 1;
     | $row = DB::run("SELECT name FROM tablename WHERE type=?", [$type])->fetchColumn();
     | (returns the string)
     |
     | # Getting required lines in the array named with one of the fields.
     | $all = DB::run("SELECT name, name2 FROM tablename")->fetchAll(PDO::FETCH_KEY_PAIR);
     | (returns the array)
     |
     | # Table update.
     | $name = 'New';
     | $option = 1;
     | $stmt = DB::run("UPDATE tablename SET name=? WHERE option=?", [$name, $option]);
     | var_dump($stmt->rowCount());
     | (returns 1 or 0)
     |
     | # Named placeholders.
     | $id  = 1;
     | $email = "mail@site.ru";
     | $row = DB::run("SELECT * FROM tablename WHERE id=:id AND email=:email", ["id" => $id, "email" => $email])->fetch()
     |
     | # IN
     | $arr = array(1,2,3);
     | $in  = str_repeat('?,', count($arr) - 1) . '?';
     | $row = DB::run("SELECT * FROM tablename WHERE column IN ($in)", $in)->fetch();
     |
     | [config key] - select the type of connection.
     |
     |
     |--------------------------------------------------------------------------------------
     |  Примеры обращения
     |--------------------------------------------------------------------------------------
     | \DB::run() -  безопасное подключение к базе вида
     | ("SELECT name_ru,icon,link_ru FROM `catalogs` WHERE `show`=? AND `type`=?", $args)
     | где $args - перечисление значений (show и type) в массиве
     |
     | # Получение одной строчки
     | $id  = 1;
     | $row = DB::run("SELECT * FROM tablename WHERE id=?", [$id])->fetch();
     | (возвращает массив)
     |
     | # Получение одного значения
     | $type = 1;
     | $row = DB::run("SELECT name FROM tablename WHERE type=?", [$type])->fetchColumn();
     | (возвращает строку)
     |
     | # Получение нужных строчек в массив, именованным одним из полей
     |  $all = DB::run("SELECT name, name2 FROM tablename")->fetchAll(PDO::FETCH_KEY_PAIR);
     | (возвращает массив)
     |
     | # Обновление таблицы
     | $name = 'New';
     | $option = 1;
     | $stmt = DB::run("UPDATE tablename SET name=? WHERE option=?", [$name, $option]);
     | var_dump($stmt->rowCount()); // проверка
     | (возвращает 1 или 0)
     |
     | # Именованные плейсхолдеры
     | $id  = 1;
     | $email = "mail@site.ru";
     | $row = DB::run("SELECT * FROM tablename WHERE id=:id AND email=:email", ["id" => $id, "email" => $email])->fetch()
     |
     | # IN
     | $arr = array(1,2,3);
     | $in  = str_repeat('?,', count($arr) - 1) . '?';
     | $row = DB::run("SELECT * FROM tablename WHERE column IN ($in)", $in)->fetch();
     |
     | [config key] - выбор типа соединения.
     |
     |--------------------------------------------------------------------------------------
    */
    public static function run($sql, $args = [], $config_key = null) {
        return \Hleb\Main\MainDB::run($sql, $args, $config_key);
    }


    /*
     |--------------------------------------------------------------------------------------
     | Regular database query like mysql.
     |--------------------------------------------------------------------------------------
     | DB::db_query("SELECT * FROM tablename WHERE name=" . $per)
     | DB::quote($per) - quoting the string values specified in the query.
     | Result:
     | $result = DB::db_query("SELECT id FROM tablename WHERE name=" . (DB::quote($per)) );
     |
     | [config key] - select the type of connection.
     |
     |
     |--------------------------------------------------------------------------------------
     | Обычный запрос в базу данных по типу mysql
     |--------------------------------------------------------------------------------------
     | DB::db_query("SELECT * FROM tablename WHERE name=" . $per)
     | DB::quote($per) - экранирование строковых значений, указываемых в запросе
     | в итоге:
     | $result = DB::db_query("SELECT id FROM tablename WHERE name=" . (DB::quote($per)) );
     |
     | [config key] - выбор типа соединения.
     |
     |--------------------------------------------------------------------------------------
    */
    public static function db_query($sql, $config_key = null) {
        return \Hleb\Main\MainDB::db_query($sql, $config_key);
    }
}


