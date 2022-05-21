<?php


namespace Hleb\Scheme\Home\Main;


interface DBInterface
{
    public function instance($configKey);
    public function run($sql, $args = [], $config = null);
    public function dbQuery(string $sql, $config = null);
}

