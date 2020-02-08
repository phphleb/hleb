<?php

declare(strict_types=1);

namespace Hleb\Constructor\Handlers;

class AddressBar
{
    const IDNACONV_PATH = '/idnaconv/idna_convert.class.php';

    private $INPUT_PARAMS;

    public $redirect = null;

    public $rel_url = null;

    public function __construct($params)
    {
        $this->INPUT_PARAMS = $params;

    }

    public function get_state(){

        $array_address = explode('?', $this->INPUT_PARAMS['SERVER']['REQUEST_URI']);

        $address = rawurldecode(array_shift($array_address));

        $rel_params = count($array_address) > 0 ? '?' . implode('?', $array_address) : '';// params

        $actual_protocol = $this->INPUT_PARAMS['HTTPS'];

        $rel_protocol = $this->INPUT_PARAMS['HLEB_PROJECT_ONLY_HTTPS'] ? 'https://' : $actual_protocol; // protocol

        define('HLEB_PROJECT_PROTOCOL', $rel_protocol);

        $end_element = explode('/', $address);

        $file_url = stripos(end($end_element), '.') === false ? false : true;

        $rel_address = "";

        if (!empty($address)) {

            $var_first = $this->INPUT_PARAMS['HLEB_PROJECT_ENDING_URL'] ? $address : mb_substr($address, 0, -1);

            $var_second = $this->INPUT_PARAMS['HLEB_PROJECT_ENDING_URL'] ? $address . '/' : $address;

            $var_all = $address[strlen($address) - 1] == '/' ? $var_first : $var_second;

            $rel_address = $file_url ? $address : $var_all; // address
        }

        $host = $this->INPUT_PARAMS['SERVER']['HTTP_HOST'];

        $idn = null;

        define('HLEB_MAIN_DOMAIN_ORIGIN', $host);

        if (stripos($host, 'xn--') !== false) {

            $idn_path = $this->INPUT_PARAMS['HLEB_PROJECT_DIRECTORY'] . self::IDNACONV_PATH;

            require("$idn_path");

            $idn = new \idna_convert(array('idn_version' => 2008));

            $host = $idn->decode($host);

        }

        $array_host = explode('.', $host);

        if ($this->INPUT_PARAMS['HLEB_PROJECT_GLUE_WITH_WWW'] == 1) {

            if ($array_host[0] == 'www') array_shift($array_host);

        } else if ($this->INPUT_PARAMS['HLEB_PROJECT_GLUE_WITH_WWW'] == 2) {

            if ($array_host[0] != 'www') $array_host = array_merge(['www'], $array_host);

        }


        $rel_host_www = implode('.', $array_host); // host

        define("HLEB_MAIN_DOMAIN", $host);

        //Проверка на валидность адреса

        if (!preg_match($this->INPUT_PARAMS['HLEB_PROJECT_VALIDITY_URL'], $address)) {

            $rel_url_main = $rel_protocol . $rel_host_www;
            self::redirect($rel_url_main);
            return $rel_url_main;

        }

        //Проверка на корректность URL

        $rel_host_www = empty($rel_address) ? $rel_host_www . $this->INPUT_PARAMS['HLEB_PROJECT_ENDING_URL'] ? '/' : "" : $rel_host_www;

        $rel_url = $rel_protocol . (preg_replace('/\/{2,}/', '/', $rel_host_www . $rel_address)) . $rel_params;

        $array_actual_uri = explode('?', $this->INPUT_PARAMS['SERVER']['REQUEST_URI']);

        $first_actual_uri = rawurldecode(array_shift($array_actual_uri));

        $first_actual_params = count($array_actual_uri) > 0 ? '?' . implode('?', $array_actual_uri) : '';

        $actual_host = is_null($idn) ? $this->INPUT_PARAMS['SERVER']['HTTP_HOST'] : $idn->decode($this->INPUT_PARAMS['SERVER']['HTTP_HOST']);

        $actual_url = $actual_protocol . $actual_host . $first_actual_uri . $first_actual_params;

        if ($rel_url !== $actual_url) {
            self::redirect($rel_url);
        }

        return $rel_url;

    }

    private function redirect($rel_url){

        $this->redirect = $rel_url;

    }
}