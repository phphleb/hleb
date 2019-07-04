<?php

declare(strict_types=1);

namespace Hleb\Constructor;

use Hleb\Constructor\Routes\Data;
use Hleb\Main\Errors\ErrorOutput;
use Hleb\Main\MyDebug;
use Hleb\Main\TryClass;
use Hleb\Main\WorkDebug;
use Phphleb\Debugpan\DPanel;
use Hleb\Main\Info;

class Workspace
{
    ///Основная обработка роута

    protected $block;

    protected $map;

    protected $hl_debug_info = ["time" => [], "block" => []];

    protected $adm_footer;

    /**
     * Workspace constructor.
     * @param array $block
     * @param array $map
     */
    function __construct(array $block, array $map)
    {
        $this->block = $block;

        $this->hl_debug_info["block"] = $block;

        $this->map = $map;

        $this->create($block);
    }

    private function create($block)
    {
        $this->calculate_time('Loading HLEB');

        $actions = $block["actions"];

        foreach ($actions as $key => $action) {

            if (isset($action["before"])) {

                $this->all_action($action["before"], "Before");

                $this->calculate_time('Class <i>' . $action["before"][0] . "</i>");

            }
        }

        //Обработчик get()

        $this->render_get_method($block);

        $this->calculate_time('Create Project');

        if (HLEB_PROJECT_DEBUG && $_SERVER['REQUEST_METHOD'] == 'GET' &&
            (new TryClass('Phphleb\Debugpan\DPanel'))->is_connect()) {

            DPanel::init($this->hl_debug_info);
        }

        foreach ($actions as $key => $action) {

            if (isset($action["after"])) {

                $this->all_action($action["after"], "After");

            }
        }

    }

    private function render_get_method($_hl_excluded_block)
    {
        $_hl_excluded_params = $_hl_excluded_block["data_params"];

        if (count($_hl_excluded_params) == 0) {

            //Загрузка контроллера

            $_hl_excluded_actions = $_hl_excluded_block["actions"];

            foreach ($_hl_excluded_actions as $_hl_exc) {

               if (isset($_hl_exc["controller"]) || isset($_hl_exc["adminPanController"])) {

                    $_hl_excluded_params =  isset($_hl_exc["controller"]) ? self::get_controller($_hl_exc["controller"]) :
                            self::get_adminPanController($_hl_exc["adminPanController"], $_hl_excluded_block);

                    if (is_array($_hl_excluded_params)) {
                        if (isset($_hl_excluded_params[2]) && $_hl_excluded_params[2] == "render") {
                            // render
                        } else {
                            $_hl_excluded_params[0] = [$_hl_excluded_params[0]];
                        }
                    } else {

                        print $_hl_excluded_params;
                        if(!empty($this->adm_footer)) print $this->adm_footer;
                        return;

                    }
                    break;
                }
            }
        }

        // Создание data() v2

        if (is_array($_hl_excluded_params) && !empty($_hl_excluded_params[1])) {

            Data::create_data($_hl_excluded_params[1]);

            $_hl_excluded_variables = $_hl_excluded_params[1];

            foreach ($_hl_excluded_variables as $_hl_excluded_key => $_hl_excluded_variable) {

                if (!is_numeric($_hl_excluded_key) && !is_numeric($_hl_excluded_key{0})) {

                    ${$_hl_excluded_key} = $_hl_excluded_variable;
                }
            }

        }


        if (isset($_hl_excluded_params["text"]) && is_string($_hl_excluded_params["text"])) {

            print $_hl_excluded_params["text"];

        } else if (isset($_hl_excluded_params[2]) && $_hl_excluded_params[2] == "views") {

            //  view(...)

            $_hl_excluded_file = str_replace("//", "/", HLEB_GLOBAL_DIRECTORY . "/resources/views/" . $_hl_excluded_params[0][0] . ".php");

            //Отображение файла

            if (file_exists($_hl_excluded_file)) {

                (new VCreator($_hl_excluded_file))->view();

            } else {
                $_hl_excluded_errors = "HL037-VIEW_ERROR: Error in function view() ! " .
                    "Missing file `/resources/views/" . $_hl_excluded_params[0][0] . ".php` . ~ " .
                    "Исключение в функции view() ! Отсутствует файл `/resources/views/" .  $_hl_excluded_params[0][0] . ".php`";

                ErrorOutput::get($_hl_excluded_errors);

            }

        } else if (isset($_hl_excluded_params[2]) && $_hl_excluded_params[2] == "render") {

            // render(...)

            $_hl_excluded_maps = $this->map;

            $_hl_excluded_errors = [];

            $_hl_excluded_params_maps = $_hl_excluded_params[0];

            Info::add("RenderMap", $_hl_excluded_params_maps);

            foreach ($_hl_excluded_params_maps as $_hl_excluded_params_map) {

                foreach ($_hl_excluded_maps as $_hl_excluded_key => $_hl_excluded_map_) {

                    if ($_hl_excluded_key == $_hl_excluded_params_map) {

                        foreach ($_hl_excluded_map_ as $_hl_excluded_map) {

                            $_hl_excluded_file = str_replace("//", "/", HLEB_GLOBAL_DIRECTORY . "/resources/views/" . $_hl_excluded_map . ".php");

                            if (file_exists($_hl_excluded_file)) {

                                require "$_hl_excluded_file";

                            } else {

                                $_hl_excluded_errors[] = "HL027-RENDER_ERROR: Error in function render() ! " .
                                    "Missing file `/resources/views/" . $_hl_excluded_map . ".php` . ~ " .
                                    "Исключение в функции render() ! Отсутствует файл `/resources/views/" . $_hl_excluded_map . ".php`";

                                ErrorOutput::add($_hl_excluded_errors);
                            }
                        }
                    }
                }
            }

            if (count($_hl_excluded_errors) > 0) ErrorOutput::run();

        }

        if(!empty($this->adm_footer)) print $this->adm_footer;
    }


    private function all_action(array $action, string $type)
    {

        //Вызов класса с методом

        $arguments = $action[1] ?? [];

        $call = explode("@", $action[0]);

        $initiator = "App\Middleware\\" . $type . "\\" . trim($call[0], "\\");

        $method = $call[1] ?? "index";

        (new $initiator())->{$method}(...$arguments);


    }


    private function get_controller(array $action)
    {
        //Вызов controller

        $arguments = $action[1] ?? [];

        $call = explode("@", $action[0]);

        $initiator = "App\Controllers\\" . trim($call[0], "\\");

        $method = $call[1] ?? "index";

        return (new $initiator())->{$method}(...$arguments);


    }

    private function get_adminPanController(array $action, $block)
    {
        //Вызов adminPanController

        $arguments = $action[1] ?? [];

        $call = explode("@", $action[0]);

        $initiator = "App\Controllers\\" . trim($call[0], "\\");

        $method = $call[1] ?? "index";

        if(!class_exists("Phphleb\Adminpan\MainAdminPanel")){
            ErrorOutput::get("HL030-ADMIN_PANEL_ERROR: Error in method adminPanController() ! " .
                "Library <a href='https://github.com/phphleb/adminpan'>phphleb/adminpan</a> not connected ! ~");
            return null;
        }

        $controller = (new $initiator())->{$method}(...$arguments);

        $adm_obj = new \Phphleb\Adminpan\Add\AdminPanHandler();

        $this->adm_footer = $adm_obj->getFooter();

        print $adm_obj->getHeader($block["number"],$block["_AdminPanelData"]);

        return $controller;
      }

    function calculate_time($name)
    {
        $num = count($this->hl_debug_info["time"]) + 1;
        $this->hl_debug_info["time"][$num . " " . $name] = round((microtime(true) - HLEB_START), 4);
    }

}


