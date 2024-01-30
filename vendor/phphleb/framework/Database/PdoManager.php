<?php

declare(strict_types=1);

namespace Hleb\Database;

use Hleb\Constructor\Attributes\Accessible;
use PDO;
use PDOStatement;

/**
 * Restricts the use of the PDO object.
 *
 * Ограничивает использование PDO-объекта.
 */
#[Accessible]
final class PdoManager
{
    protected PDO $pdo;

    protected ?string $driver = null;

    /**
     * @internal
     */
    public function __construct(#[\SensitiveParameter] PDO $pdo, readonly string $configKey)
    {
        $this->pdo = $pdo;
        $this->driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    /**
     * @see PDO::beginTransaction()
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Returns the name of the connection
     * from the framework configuration.
     *
     * Возвращает название подключения
     * из конфигурации фреймворка.
     */
    public function getLabel(): string
    {
        return $this->configKey;
    }

    /**
     * @see PDO::commit()
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * @see PDO::rollBack()
     */
    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * @see PDO::errorCode()
     */
    public function errorCode()
    {
        return $this->pdo->errorCode();
    }

    /**
     * @see PDO::errorInfo()
     */
    public function errorInfo(): array
    {
        return $this->pdo->errorInfo();
    }

    /**
     * @see PDO::exec()
     */
    public function exec(#[\SensitiveParameter] string $statement): false|int
    {

        SystemDB::createLog(\microtime(true), $statement, $this->configKey, previously: true, driver: $this->driver);

        return $this->pdo->exec($statement);
    }

    /**
     * @see PDO::getAttribute()
     */
    public function getAttribute(#[\SensitiveParameter] int $attribute): mixed
    {
        return $this->pdo->getAttribute($attribute);
    }

    /**
     * @see PDO::getAvailableDrivers()
     */
    public function getAvailableDrivers(): array
    {
        return $this->pdo::getAvailableDrivers();
    }

    /**
     * @see PDO::inTransaction()
     */
    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    /**
     * @see PDO::lastInsertId()
     */
    public function lastInsertId($name = null): false|string
    {
        return $this->pdo->lastInsertId($name);
    }

    /**
     * @see PDO::prepare()
     */
    public function prepare( #[\SensitiveParameter] string $query,  #[\SensitiveParameter] array  $options = []): false|PDOStatement
    {
        SystemDB::createLog(\microtime(true), $query, $this->configKey, 'prepare', previously: true, driver: $this->driver);

        return $this->pdo->prepare($query, $options);
    }

    /**
     * @see PDO::query()
     */
    public function query(#[\SensitiveParameter] string $query, int|null $fetchMode = null): false|PDOStatement
    {
        SystemDB::createLog(\microtime(true), $query, $this->configKey, previously: true, driver: $this->driver);

        return $this->pdo->query($query, $fetchMode);
    }

    /**
     * @see PDO::quote()
     */
    public function quote(#[\SensitiveParameter] string $query, int|null $fetchMode = null): false|string
    {
        return $this->pdo->quote($query, $fetchMode);
    }

}
