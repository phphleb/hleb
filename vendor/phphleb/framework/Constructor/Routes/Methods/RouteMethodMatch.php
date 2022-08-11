<?php

declare(strict_types=1);

/*
 * Main PUT-route processing.
 *
 * Обработка основного PUT-роута.
 */

namespace Hleb\Constructor\Routes\Methods;


use Hleb\Main\Errors\ErrorOutput;
use Hleb\Scheme\Home\Constructor\Routes\StandardRoute;

class RouteMethodMatch extends RouteMethodGet
{
    protected $httpTypes = [];

    public function __construct(StandardRoute $instance, array $types, string $routePath, $params = [])
    {
        $this->httpTypes = $this->validate($types);
        parent::__construct($instance, $routePath, $params);

    }
    // The unique name of the route.
    // Уникальный идентификатор роута.
    protected function getName() {
          return 'match';
    }

    // The request type for the route.
    // Тип запроса для роута.
    protected function getHttpMethodType() {
        return $this->httpTypes;
    }

    private function validate(array $types) {
        if (empty($types)) {
            $this->errors[] = "HL057-ROUTE_ERROR: 'types' value must not be empty on method ->match(types, path, params) ! " .
                "Expected values: " . implode(', ', HLEB_HTTP_TYPE_SUPPORT) . " ~ " .
                "Значение 'types' не должно быть пустым в методе ->match(types, path, params) ! Ожидались возможные значения:  " . implode(', ', HLEB_HTTP_TYPE_SUPPORT);
            ErrorOutput::add($this->errors);
        }

        foreach ($types as $key => $type) {
            $types[$key] = strtolower($type);
        }

        foreach ($types as $key => $type) {
            if (!in_array($type,HLEB_HTTP_TYPE_SUPPORT)) {
                $this->errors[] = "HL056-ROUTE_ERROR: Wrong composition of 'types' argument on method ->match(types, path, params) ! " .
                    "Expected values: " . implode(', ', HLEB_HTTP_TYPE_SUPPORT) . " ~ " .
                    "Неправильное значение аргумента 'types' в методе ->match(types, path, params) ! Ожидались возможные значения:  " . implode(', ', HLEB_HTTP_TYPE_SUPPORT);
                ErrorOutput::add($this->errors);
            }

        }
        return $types;
    }

}

