<?php

namespace Hleb\Main;

class AsyncClearConnector
{
    public static function clearAll()
    {
        $staticClasses = [
            'Hleb\Main\Insert\Examples\ExampleApp',
            'Hleb\Constructor\Handlers\Request',
            'Hleb\Constructor\Routes\Data',
            'Hleb\Constructor\Handlers\Key',
            'Hleb\Constructor\Handlers\ProtectedCSRF',
            'Hleb\Main\Insert\PageFinisher',
            'Hleb\Main\DataDebug',
            'Hleb\Main\WorkDebug',
            'Hleb\Main\Info',
            'Hleb\Main\Errors\ErrorOutput',
            'Hleb\Constructor\Handlers\URL',
            'Phphleb\Debugpan\DPanel',
            'Hleb\Main\MainDB',
        ];
        foreach ($staticClasses as $class) {
            if (class_exists($class, false) && method_exists($class, 'clear')) {
                $class::clear();
            }
        }
    }
}