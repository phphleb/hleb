<?php

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Database\PdoManager;
use Hleb\Database\SystemDB;
use Hleb\Main\Insert\ContainerUniqueItem;
use PDO;

/**
 * A wrapper for working with PDO.
 *
 * Оболочка для работы с PDO.
 *
 * @see DbInterface
 */
#[Accessible] #[AvailableAsParent]
class DbReference extends ContainerUniqueItem implements DbInterface, Interface\Db
{
    /**
     * @see DbInterface::run()
     */
    #[\Override]
    public function run(#[\SensitiveParameter] string $sql, #[\SensitiveParameter] array $args = [], ?string $configKey = null): false|\PDOStatement
    {
        return SystemDB::run($sql, $args, $configKey);
    }

    /**
     * @see DbInterface::dbQuery()
     */
    #[\Override]
    public function dbQuery(#[\SensitiveParameter] string $sql, ?string $configKey = null): false|array
    {
        return SystemDB::dbQuery($sql, $configKey);
    }

    /*
     │--------------------------------------------------------------------------------------
     │ Returns a PDO object initialized from the current or specified configuration.
     │--------------------------------------------------------------------------------------
     │ $db->getPdoInstance()->getAttribute(\PDO::ATTR_DRIVER_NAME);
     │ // similarly
     │ $db->getPdoInstance('mysql.name')->getAttribute(\PDO::ATTR_DRIVER_NAME);
     │
     │
     │--------------------------------------------------------------------------------------
     │ Возвращает инициализированный из текущей или указанной конфигурации объект PDO.
     │--------------------------------------------------------------------------------------
     │  $db->getPdoInstance()->getAttribute(\PDO::ATTR_DRIVER_NAME);
     │  // аналогично
     │  $db->getPdoInstance('mysql.name')->getAttribute(\PDO::ATTR_DRIVER_NAME);
     │
     │--------------------------------------------------------------------------------------
    */
    public function getPdoInstance(?string $configKey = null): PdoManager
    {
        return SystemDB::getPdoInstance($configKey);
    }

    /*
     │--------------------------------------------------------------------------------------
     │ Returns a new PDO object configured according to the framework settings.
     │--------------------------------------------------------------------------------------
     │ A specific configuration key may be given.
     │
     │
     │--------------------------------------------------------------------------------------
     │ Возвращает новый объект PDO, сконфигурированный согласно настройкам фреймворка.
     │--------------------------------------------------------------------------------------
     │ Может быть задан конкретный ключ конфигурации.
     │
     │--------------------------------------------------------------------------------------
    */
    public function getNewPdoInstance(?string $configKey = null): PDO
    {
        return SystemDB::getNewPdoInstance($configKey);
    }

    /*
     │--------------------------------------------------------------------------------------
     │ Returns the current configuration or one defined by key.
     │--------------------------------------------------------------------------------------
     │ $config = $db->getConfig();
     │ $dbName = $config['dbname] ?? null;
     │
     │
     │--------------------------------------------------------------------------------------
     │ Возвращает актуальную конфигурацию или определённую по ключу.
     │--------------------------------------------------------------------------------------
     │ $config = $db->getConfig();
     │ $dbName = $config['dbname] ?? null;
     │
     │--------------------------------------------------------------------------------------
    */
    public function getConfig(?string $configKey = null): ?array
    {
        return SystemDB::getConfig($configKey);
    }

    /*
     │--------------------------------------------------------------------------------------
     │ Escaping a value.
     │--------------------------------------------------------------------------------------
     │ Экранирование значения.
     │--------------------------------------------------------------------------------------
    */
    #[\Override]
    public function quote(string $value, int $type = PDO::PARAM_STR, ?string $config = null): string
    {
        return SystemDB::quote($value, $type, $config);
    }

    /*
     │--------------------------------------------------------------------------------------
     │ Delete the current connection.
     │--------------------------------------------------------------------------------------
     │ Удаление текущего соединения.
     │--------------------------------------------------------------------------------------
    */
    #[\Override]
    public static function rollback(): void
    {
       SystemDB::rollback();
    }
}
