<?php

class DB
{
     use DeterminantStaticUncreated;

    public static function instance()
    {
        if (self::$instance === null) {
            $opt = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => TRUE,
            );

            include HLEB_GLOBAL_DIRECTORY . "/database/dbase.config.php";

            $prms = HLEB_PARAMETERS_FOR_DB[HLEB_TYPE_DB];

            $connection = "";

            switch(HLEB_TYPE_DB){
                case "mysql":
                    $connection = "mysql:host=" . $prms["DB_HOST"] . ";port=" . $prms["DB_PORT"] . ";dbname=" . $prms["DB_NAME"] . ";charset=" . $prms["DB_CHAR"];
                    break;

                case "postgresql":
                    $connection = "pgsql:host=" . $prms["DB_HOST"] . ";port=" . $prms["DB_PORT"] . ";dbname=" . $prms["DB_NAME"];
                    break;

                case "sqlite":
                    $connection= "sqlite:" . $prms["DB_PATH"];
                    break;
            }

            $user = $prms["DB_USER"];

            $pass = $prms["DB_PASS"];

            self::$instance = new PDO($connection, $user, $pass, $opt);
        }
        return self::$instance;
    }

    public static function run($sql, $args = array())
    {
        $stmt = self::instance()->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }

    /*  Примеры обращения

    \DB::run() -  безопасное подключение к базе вида ("SELECT name_ru,icon,link_ru FROM `catalogs` WHERE `show`=? AND `type`=?", $args)
    где $args - перечисление значений (show и type) в массиве

    # Получение одной строчки
    $id  = 1;
    $row = DB::run("SELECT * FROM tablename WHERE id=?", [$id])->fetch();
    (возвращает массив)

    # Получение одного значения
    $type = 1;
    $row = DB::run("SELECT name FROM tablename WHERE type=?", [$type])->fetchColumn();
    (возвращает строку)

    # Получение нужных строчек в массив, именованным одним из полей
    $all = DB::run("SELECT name, name2 FROM tablename")->fetchAll(PDO::FETCH_KEY_PAIR);
    (возвращает массив)

    # Обновление таблицы
    $name = 'New';
    $option = 1;
    $stmt = DB::run("UPDATE tablename SET name=? WHERE option=?", [$name, $option]);
    var_dump($stmt->rowCount()); // проверка
    (возвращает 1 или 0)

    # Именованные плейсхолдеры
    $id  = 1;
    $email = "mail@site.ru";
    $row = DB::run("SELECT * FROM tablename WHERE id=:id AND email=:email", ["id" => $id, "email" => $email])->fetch()

    # IN
    $arr = array(1,2,3);
    $in  = str_repeat('?,', count($arr) - 1) . '?';
    $row = DB::run("SELECT * FROM tablename WHERE column IN ($in)", $in)->fetch();

     */

    public static function db_query($sql)
    {
        $stmt = self::query($sql);
        $data = $stmt->fetchAll();
        return $data;
    }

    /*
    db_query

    Обычный запрос в базу данных по типу mysgl
     DB::db_query("SELECT * FROM tablename WHERE name=".$per)
     DB::quote($per) - экранирование строковых значений, указываемых в запросе
    в итоге:
    DB::db_query("SELECT id FROM tablename WHERE name=".(DB::quote($per)) );
     */



}
