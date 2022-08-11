<?php

namespace Hleb\Scheme\Home\Constructor\Routes;

abstract class DataRoute
{
    protected $dataName;

    protected $dataPath;

    protected $dataParams;

    protected $type;

    protected $httpTypes;

    protected $actions;

    protected $methodName;

    protected $methodTypeName;

    protected $methodData;

    protected $errors;

    protected $controllerPath;

    protected function createMethodData(){}

}

