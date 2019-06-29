<?php

namespace Hleb\Constructor\Handlers;

class AddressBar
{
    const IDNACONV_PATH = "/idnaconv/idna_convert.class.php";

    private $INPUT_PARAMS;

    public $redirect = null;

    public $rel_url = null;

    public function __construct($params)
    {
        $this->INPUT_PARAMS = $params;

        $this->set();
    }

    private function set(){

        $val_array_address = explode("?", $this->INPUT_PARAMS['SERVER']['REQUEST_URI']);

        $val_address = rawurldecode(array_shift($val_array_address));

        $rel_params = count($val_array_address) > 0 ? "?" . implode("?", $val_array_address) : "";// params

        $actual_protocol = $this->INPUT_PARAMS["HTTPS"];

        $rel_protocol = $this->INPUT_PARAMS['HLEB_PROJECT_ONLY_HTTPS'] ? "https://" : $actual_protocol; // protocol

        define("HLEB_PROJECT_PROTOCOL", $rel_protocol);

        $end_element = explode("/", $val_address);

        $file_url = stripos(end($end_element), ".") === false ? false : true;

        $rel_address = "";

        if (!empty($val_address)) {

            $var_first = $this->INPUT_PARAMS['HLEB_PROJECT_ENDING_URL'] ? $val_address : mb_substr($val_address, 0, -1);

            $var_second = $this->INPUT_PARAMS['HLEB_PROJECT_ENDING_URL'] ? $val_address . "/" : $val_address;

            $var_all = $val_address{strlen($val_address) - 1} == "/" ? $var_first : $var_second;

            $rel_address = $file_url ? $val_address : $var_all; // address
        }

        $val_host = $this->INPUT_PARAMS['SERVER']['HTTP_HOST'];

        $idn = null;

        define("HLEB_MAIN_DOMAIN_ORIGIN", $val_host);

        if (stripos($val_host, 'xn--') !== false) {

            $idn_path = $this->INPUT_PARAMS['HLEB_PROJECT_DIRECTORY'] . self::IDNACONV_PATH;

            include("$idn_path");

            $idn = new idna_convert(array('idn_version' => 2008));

            $val_host = $idn->decode($val_host);

        }

        $val_array_host = explode(".", $val_host);

        if ($this->INPUT_PARAMS['HLEB_PROJECT_GLUE_WITH_WWW'] == 1) {

            if ($val_array_host[0] == "www") array_shift($val_array_host);

        } else if ($this->INPUT_PARAMS['HLEB_PROJECT_GLUE_WITH_WWW'] == 2) {

            if ($val_array_host[0] != "www") $val_array_host = array_merge(["www"], $val_array_host);

        }


        $rel_host_www = implode(".", $val_array_host); // host

        define("HLEB_MAIN_DOMAIN", $val_host);

        //Проверка на валидность адреса

        if (!preg_match($this->INPUT_PARAMS['HLEB_PROJECT_VALIDITY_URL'], $val_address)) {

            self::redirect($rel_protocol . $rel_host_www);
        }

        //Проверка на корректность URL

        $rel_host_www = empty($rel_address) ? $rel_host_www . $this->INPUT_PARAMS['HLEB_PROJECT_ENDING_URL'] ? "/" : "" : $rel_host_www;

        $rel_url = $rel_protocol . (preg_replace("/\/{2,}/", "/", $rel_host_www . $rel_address)) . $rel_params;

        $val_array_actual_uri = explode("?", $this->INPUT_PARAMS['SERVER']['REQUEST_URI']);

        $val_first_actual_uri = rawurldecode(array_shift($val_array_actual_uri));

        $val_first_actual_params = count($val_array_actual_uri) > 0 ? "?" . implode("?", $val_array_actual_uri) : "";

        $val_actual_host = $idn === null ? $this->INPUT_PARAMS['SERVER']['HTTP_HOST'] : $idn->decode($this->INPUT_PARAMS['SERVER']['HTTP_HOST']);

        $actual_url = $actual_protocol . $val_actual_host . $val_first_actual_uri . $val_first_actual_params;

        if ($rel_url !== $actual_url) {

            self::redirect($rel_url);

        }

    }

    private function redirect($rel_url){

        $this->redirect = $rel_url;

        header('Location: ' . $this->redirect, true, 301);
        exit();

    }
}