<?php

namespace Hleb\Reference;

use PDO;

/**
 * For backward compatibility with custom containers,
 * this interface can only be extended.
 *
 * Для обратной совместимостью с пользовательскими контейнерами
 * этот интерфейс может только расширяться.
 */
interface DbInterface
{
    /**
     * ## Examples of appeals
     * ```php
     * // Secure connection to the database of the form:
     * $db->run("SELECT name,icon,link FROM cat WHERE show=? AND type=?", $args);
     * // Where $args is an enumeration of the values (show and type) in the array.
     *```
     * ### Getting one line
     * ```php
     * $id  = 1;
     * $row = $db->run("SELECT * FROM table_name WHERE id=?", [$id])->fetch();
     * // Returns an array.
     * ```
     * ### Getting one value
     * ```php
     * $type = 1;
     * $row = $db->run("SELECT name FROM table_name WHERE type=?", [$type])
     *     ->fetchColumn();
     * // Returns a string.
     * ```
     * ### Getting required lines in the array named with one of the fields.
     * ```php
     * $all = $db->run("SELECT name, name2 FROM table_name")
     *     ->fetchAll(PDO::FETCH_KEY_PAIR);
     * // Returns an array.
     *```
     * ### Table update
     * ```php
     * $name = 'New';
     * $option = 1;
     * $stmt = $db->run("UPDATE table_name SET name=? WHERE option=?", [$name, $option]);
     * var_dump($stmt->rowCount());
     * // Returns 1 or 0.
     * ```
     * ### Named placeholders
     * ```php
     * $id  = 1;
     * $email = "mail@site.ru";
     * $row = $db->run("SELECT * FROM table_name WHERE id=:id AND email=:email",
     *        ["id" => $id, "email" => $email]
     *    )->fetch();
     *```
     * ### IN
     * ```php
     * $arr = [1,2,3];
     * $in  = str_repeat('?,', count($arr) - 1) . '?';
     * $row = $db->run("SELECT * FROM table_name WHERE column IN ($in)", $arr)->fetch();
     *```
     *
     * ## Примеры обращения
     * ```php
     * // Безопасное подключение к базе вида:
     * $db->run("SELECT name,icon,link FROM `cat` WHERE `show`=? AND `type`=?", $args);
     * // Где $args - перечисление значений (show и type) в массиве.
     *```
     * ### Получение одной строчки
     * ```php
     * $id  = 1;
     * $row = $db->run("SELECT * FROM table_name WHERE id=?", [$id])->fetch();
     * // Возвращает массив.
     *```
     * ### Получение одного значения
     * ```php
     * $type = 1;
     * $row = $db->run("SELECT name FROM table_name WHERE type=?", [$type])
     *    ->fetchColumn();
     * // Возвращает строку.
     *```
     * ### Получение нужных строчек в массив, именованным одним из полей
     * ```php
     * $all = $db->run("SELECT name, name2 FROM table_name")
     *    ->fetchAll(PDO::FETCH_KEY_PAIR);
     * // Возвращает массив.
     *```
     * ### Обновление таблицы
     * ```php
     * $name = 'New';
     * $option = 1;
     * $stmt = $db->run("UPDATE table_name SET name=? WHERE option=?", [$name, $option]);
     * var_dump($stmt->rowCount()); // проверка
     * // Возвращает 1 или 0.
     * ```
     *
     * ### Именованные плейсхолдеры
     * ```php
     * $id  = 1;
     * $email = "mail@site.ru";
     * $row = $db->run("SELECT * FROM table_name WHERE id=:id AND email=:email", ["id" => $id, "email" => $email])->fetch()
     *```
     * ### IN
     * ```php
     * $arr = [1,2,3];
     * $in  = str_repeat('?,', count($arr) - 1) . '?';
     * $row = $db->run("SELECT * FROM table_name WHERE column IN ($in)", $arr)->fetch();
     *```
     * @param string|null $configKey - select the type of connection.
     *                               - выбор типа соединения.
     */
    public function run(string $sql, array $args = [], ?string $configKey = null): false|\PDOStatement;

    /**
     * Regular database query like mysql.
     * ```php
     * // Quoting the string values specified in the query.
     * $param = $db->quote($param);
     * $result = $db->dbQuery("SELECT id FROM table_name WHERE name='{$param}'");
     * ```
     *
     * Обычный запрос в базу данных по типу mysql.
     *
     * ```php
     * // Экранирование строковых значений, указываемых в запросе.
     * $param = $db->quote($param);
     * $result = $db->dbQuery("SELECT id FROM table_name WHERE name='{$param}'");
     *```
     * @param string|null $configKey - select the type of connection.
     *                               - выбор типа соединения.
     */
    public function dbQuery(string $sql, ?string $configKey = null): false|array;

    /**
     * Escaping a value.
     *
     * Экранирование значения.
     */
    public function quote(string $value, int $type = PDO::PARAM_STR, ?string $config = null): string;

    /*
     * Delete the current connection.
     *
     * Удаление текущего соединения.
    */
    public static function rollback(): void;
}
