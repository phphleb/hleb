<?php

declare(strict_types=1);

/*
 * General class for routes.
 *
 * Общий класс для роутов.
 */

namespace Hleb\Constructor\Routes;

use \Closure;
use Hleb\Scheme\Home\Constructor\Routes\DataRoute;
use Hleb\Scheme\Home\Constructor\Routes\{
    RouteMethodStandard
};

class MainRouteMethod extends DataRoute implements RouteMethodStandard
{

    protected $dataName = null;

    protected $dataPath = null;

    protected $dataParams = [];

    protected $type = [];

    protected $httpTypes = HLEB_HTTP_TYPE_SUPPORT;

    protected $actions = [];

    protected $protect = [];

    protected $methodTypeName = null;

    protected $errors = [];

    protected $domain = [];

    /**
     * @param object|Closure|string $obj
     * @return array|string
     */
    public function calculateIncomingObject($obj) {
        if (is_object($obj)) {
            return $obj();
        }
        return $obj;
    }

    // Formation of information about the error.
    // Формирование информации об ошибке.
    public function error() {
        return implode(', ', $this->errors);
    }

    // Check for detected errors.
    // Проверка на обнаруженные ошибки.
    public function approved() {
        if (count($this->errors) > 0) {
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function data() {
        $this->createMethodData();
        $this->methodData['number']++;
        return $this->methodData;
    }

    // Устанавливает сформированные данные метода.
    protected function createMethodData() {
        $this->methodData =
            [
                'number' => 1000,
                'data_name' => $this->dataName,
                'data_path' => $this->dataPath,
                'data_params' => $this->dataParams,
                'type' => $this->type,
                'actions' => $this->actions,
                'protect' => array_unique($this->protect),
                'method_type_name' => $this->methodTypeName,
                'domain' => $this->domain,
            ];
    }
}

