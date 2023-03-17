<?php

declare(strict_types=1);

namespace Hleb\Main\Insert\Examples;


use Hleb\Main\PdoManager;

/**
 * @see ExampleApp
 */
class ExampleMirrorDB
{
    private $db = [];

    /**
     * @param array $list -  Adding test values to be returned in methods.
     *
     *                    -  Добавление тестовых значений, которые будут возвращены в методах.
     */
    public function __construct(array $list)
    {
        $this->db = $list;
    }

    // Call stub.
    public static function getInstance()
    {
        return new self;
    }

    public static function instance($configKey)
    {
        return new self;
    }

    public function run($sql, $args = [], $config = null)
    {
        return $this->db['run'];
    }

    public function dbQuery(string $sql, $config = null)
    {
        return $this->db['dbQuery'];
    }

    public function getPdoInstance($configKey = null): PdoManager
    {
        return $this->db['getPdoInstance'];
    }
}

