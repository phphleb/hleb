<?php


namespace Hleb\Main;


use Hleb\Main\Insert\BaseSingleton;

class MirrorDB extends BaseSingleton
{
    public function instance($configKey) {
       return MainDB::instance($configKey);
    }
    public function run($sql, $args = [], $config = null) {
       return MainDB::run($sql, $args, $config);
    }
    public function dbQuery(string $sql, $config = null) {
        return MainDB::run($sql, $config);
    }
    public function getPdoInstance($configKey = null): PdoManager
    {
        return MainDB::getPdoInstance($configKey);
    }
}

