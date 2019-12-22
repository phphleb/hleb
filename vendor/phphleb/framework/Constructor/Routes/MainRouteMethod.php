<?php

declare(strict_types=1);

namespace Hleb\Constructor\Routes;

use \Closure;
use Hleb\Scheme\Home\Constructor\Routes\DataRoute;
use Hleb\Scheme\Home\Constructor\Routes\{
    RouteMethodStandard
};

class MainRouteMethod extends DataRoute implements RouteMethodStandard
{

//======= DEFAULT METHOD ========//


    protected $data_name = null;

    protected $data_path = null;

    protected $data_params = [];

    protected $type = [];

    protected $types = HLEB_HTTP_TYPE_SUPPORT;

    protected $actions = [];

    protected $protect = [];

    protected $method_type_name = null;

    protected $errors = [];

    protected $domain = [];


//================================//

    /**
     * @param object|Closure|string $obj
     * @return array|string
     */
    public function calculate_incoming_object($obj)
    {

        if (is_object($obj)) {

            return $obj();
        }

        return $obj;

    }

    public function error()
    {

        return implode(', ', $this->errors);
    }


    public function approved()
    {

        if (count($this->errors) > 0) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function data()
    {

        $this->create_method_data();

        $this->method_data['number']++;

        return $this->method_data;
    }



    protected function create_method_data()
    {
        $this->method_data =
            [
                'number' => 1000,

                'data_name' => $this->data_name,

                'data_path' => $this->data_path,

                'data_params' => $this->data_params,

                'type' => $this->type,

                'actions' => $this->actions,

                'protect' => array_unique($this->protect),

                'method_type_name' => $this->method_type_name,

                'domain' => $this->domain,

            ];

    }


}

