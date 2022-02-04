<?php


namespace Hleb\Main;


use PDO;
use PDOStatement;

class PdoManager
{
    protected $pdo;

    /**
     * @internal
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @see PDO::beginTransaction()
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * @see PDO::commit()
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * @see PDO::rollBack()
     */
    public function rollBack()
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
    public function errorInfo()
    {
        return $this->pdo->errorInfo();
    }

    /**
     * @see PDO::exec()
     * @param string $statement
     * @return false|int
     */
    public function exec(string $statement)
    {
        return $this->pdo->exec($statement);
    }

    /**
     * @see PDO::getAttribute()
     * @param int $attribute
     * @return mixed
     */
    public function getAttribute(int $attribute)
    {
        return $this->pdo->getAttribute($attribute);
    }

    /**
     * @see PDO::getAvailableDrivers()
     */
    public function getAvailableDrivers()
    {
        return $this->pdo->getAvailableDrivers();
    }

    /**
     * @see PDO::inTransaction()
     */
    public function inTransaction()
    {
        return $this->pdo->inTransaction();
    }

    /**
     * @see PDO::lastInsertId()
     */
    public function lastInsertId($name = null)
    {
        return $this->pdo->lastInsertId($name);
    }

    /**
     * @see PDO::prepare()
     * @param string $query
     * @param array $options
     * @return bool|PDOStatement
     */
    public function prepare(string $query, array $options = [])
    {
        return $this->pdo->prepare($query, $options);
    }

    /**
     * @see PDO::query()
     * @param string $query
     * @param  int|null $fetchMode
     * @return false|PDOStatement
     */
    public function query(string $query, $fetchMode = null)
    {
        return $this->pdo->query($query, $fetchMode);
    }

    /**
     * @see PDO::quote()
     * @param string $query
     * @param int|null $fetchMode
     * @return false|string
     */
    public function quote(string $query, $fetchMode = null)
    {
        return $this->pdo->quote($query, $fetchMode);
    }

}

