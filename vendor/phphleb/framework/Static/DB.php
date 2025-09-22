<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Database\PdoManager;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Reference\DbInterface;
use PDO;

/**
 * A wrapper for working with PDO.
 *
 * Оболочка для работы с PDO.
 */
#[Accessible]
final class DB extends BaseSingleton
{
    private static DbInterface|null $replace = null;

    /**
     * ## Examples of appeals
     * ```php
     * // Secure connection to the database of the form:
     * DB::run("SELECT name,icon,link FROM cat WHERE show=? AND type=?", $args);
     * // Where $args is an enumeration of the values (show and type) in the array.
     *```
     * ### Getting one line
     * ```php
     * $id  = 1;
     * $row = DB::run("SELECT * FROM table_name WHERE id=?", [$id])->fetch();
     * // Returns an array.
     * ```
     * ### Getting one value
     * ```php
     * $type = 1;
     * $row = DB::run("SELECT name FROM table_name WHERE type=?", [$type])
     *     ->fetchColumn();
     * // Returns a string.
     * ```
     * ### Getting required lines in the array named with one of the fields.
     * ```php
     * $all = DB::run("SELECT name, name2 FROM table_name")
     *     ->fetchAll(PDO::FETCH_KEY_PAIR);
     * // Returns an array.
     *```
     * ### Table update
     * ```php
     * $name = 'New';
     * $option = 1;
     * $stmt = DB::run("UPDATE table_name SET name=? WHERE option=?", [$name, $option]);
     * var_dump($stmt->rowCount());
     * // Returns 1 or 0.
     * ```
     * ### Named placeholders
     * ```php
     * $id  = 1;
     * $email = "mail@site.ru";
     * $row = DB::run("SELECT * FROM table_name WHERE id=:id AND email=:email",
     *        ["id" => $id, "email" => $email]
     *    )->fetch();
     *```
     * ### IN
     * ```php
     * $arr = [1,2,3];
     * $in  = str_repeat('?,', count($arr) - 1) . '?';
     * $row = DB::run("SELECT * FROM table_name WHERE column IN ($in)", $arr)->fetch();
     *```
     *
     * ## Примеры обращения
     * ```php
     * // Безопасное подключение к базе вида:
     * DB::run("SELECT name,icon,link FROM `cat` WHERE `show`=? AND `type`=?", $args);
     * // Где $args - перечисление значений (show и type) в массиве.
     *```
     * ### Получение одной строчки
     * ```php
     * $id  = 1;
     * $row = DB::run("SELECT * FROM table_name WHERE id=?", [$id])->fetch();
     * // Возвращает массив.
     *```
     * ### Получение одного значения
     * ```php
     * $type = 1;
     * $row = DB::run("SELECT name FROM table_name WHERE type=?", [$type])
     *    ->fetchColumn();
     * // Возвращает строку.
     *```
     * ### Получение нужных строчек в массив, именованным одним из полей
     * ```php
     * $all = DB::run("SELECT name, name2 FROM table_name")
     *    ->fetchAll(PDO::FETCH_KEY_PAIR);
     * // Возвращает массив.
     *```
     * ### Обновление таблицы
     * ```php
     * $name = 'New';
     * $option = 1;
     * $stmt = DB::run("UPDATE table_name SET name=? WHERE option=?", [$name, $option]);
     * var_dump($stmt->rowCount()); // проверка
     * // Возвращает 1 или 0.
     * ```
     *
     * ### Именованные плейсхолдеры
     * ```php
     * $id  = 1;
     * $email = "mail@site.ru";
     * $row = DB::run("SELECT * FROM table_name WHERE id=:id AND email=:email", ["id" => $id, "email" => $email])->fetch()
     *```
     * ### IN
     * ```php
     * $arr = [1,2,3];
     * $in  = str_repeat('?,', count($arr) - 1) . '?';
     * $row = DB::run("SELECT * FROM table_name WHERE column IN ($in)", $arr)->fetch();
     *```
     * @param string|null $configKey - select the type of connection.
     *                               - выбор типа соединения.
     */
    public static function run(#[\SensitiveParameter] string $sql, #[\SensitiveParameter] array $args = [], ?string $configKey = null): false|\PDOStatement
    {
        if (self::$replace) {
            return self::$replace->run($sql, $args, $configKey);
        }

        return BaseContainer::instance()->get(DbInterface::class)->run($sql, $args, $configKey);
    }


    /**
     * Regular database query like mysql.
     * ```php
     * // Quoting the string values specified in the query.
     * $param = DB::quote($param);
     * $result = DB::dbQuery("SELECT id FROM table_name WHERE name='{$param}'");
     * ```
     *
     * Обычный запрос в базу данных по типу mysql.
     *
     * ```php
     * // Экранирование строковых значений, указываемых в запросе.
     * $param = DB::quote($param);
     * $result = DB::dbQuery("SELECT id FROM table_name WHERE name='{$param}'");
     *```
     * @param string|null $configKey - select the type of connection.
     *                               - выбор типа соединения.
     */
    public static function dbQuery(#[\SensitiveParameter] string $sql, ?string $configKey = null): false|array
    {
        if (self::$replace) {
            return self::$replace->dbQuery($sql, $configKey);
        }

        return BaseContainer::instance()->get(DbInterface::class)->dbQuery($sql, $configKey);
    }

    /**
     * Returns a PDO object initialized from the current or specified configuration.
     *
     * Возвращает инициализированный из текущей или указанной конфигурации объект PDO.
     *
     * ```php
     *  DB::getPdoInstance()->getAttribute(\PDO::ATTR_DRIVER_NAME);
     *  // or
     *  DB::getPdoInstance('mysql.name')->getAttribute(\PDO::ATTR_DRIVER_NAME);
     * ```
     */
    public static function getPdoInstance(?string $configKey = null): PdoManager
    {
        if (self::$replace) {
            return self::$replace->getPdoInstance($configKey);
        }

        return BaseContainer::instance()->get(DbInterface::class)->getPdoInstance($configKey);
    }

    /**
     * Returns a new PDO object configured according to the framework settings.
     * A specific configuration key may be given.
     *
     * Возвращает новый объект PDO, сконфигурированный согласно настройкам фреймворка.
     * Может быть задан конкретный ключ конфигурации.
     */
    public static function getNewInstance(?string $configKey = null): PDO
    {
        if (self::$replace) {
            return self::$replace->getNewPdoInstance($configKey);
        }

        return BaseContainer::instance()->get(DbInterface::class)->getNewPdoInstance($configKey);
    }

    /**
     * Returns the current configuration or one defined by key.
     *
     * Возвращает актуальную конфигурацию или определённую по ключу.
     *
     * ```php
     *  $config = DB::getConfig();
     *  $dbName = $config['dbname'] ?? null;
     * ```
     */
    public static function getConfig(?string $configKey = null): ?array
    {
        if (self::$replace) {
            return self::$replace->getConfig($configKey);
        }

        return BaseContainer::instance()->get(DbInterface::class)->getConfig($configKey);
    }

    /**
     * Escaping a value.
     *
     * Экранирование значения.
     */
    public static function quote(string $value, int $type = PDO::PARAM_STR, ?string $config = null): string
    {
        if (self::$replace) {
            return self::$replace->quote($value, $type, $config);
        }

        return BaseContainer::instance()->get(DbInterface::class)->quote($value, $type, $config);
    }

    /**
     * @internal
     *
     * @see DbForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(DbInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
