<?php

declare(strict_types=1);

namespace Hleb\Main;

use Hleb\Main\Insert\BaseSingleton;
use Hleb\Scheme\Home\Main\DBInterface;

class MirrorDB extends BaseSingleton implements DBInterface
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

