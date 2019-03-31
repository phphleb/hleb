<?php

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

    protected $hl_content_create = false;

    protected $hl_debug_info = ["time" => [], "block" => []];

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
        if ($this->hl_content_create) return;

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
        if ($this->hl_content_create) return;

        $_hl_excluded_params = $_hl_excluded_block["data_params"];

        if (count($_hl_excluded_params) == 0) {

            //Загрузка контроллера

            $_hl_excluded_actions = $_hl_excluded_block["actions"];

            foreach ($_hl_excluded_actions as $_hl_excluded_action) {

                if (isset($_hl_excluded_action["controller"])) {

                    $_hl_excluded_params = self::get_controller($_hl_excluded_action["controller"]);

                    if (gettype($_hl_excluded_params) == "array") {

                        if (isset($_hl_excluded_params[2]) && $_hl_excluded_params[2] == "render") {
                            // render
                        } else {
                            $_hl_excluded_params[0] = [$_hl_excluded_params[0]];
                        }

                    } else {

                        $this->hl_content_create = true;

                        print $_hl_excluded_params;


                        return;
                    }

                    break;
                }

            }


        }

        // Создание data() v2


        if (gettype($_hl_excluded_params) == "array" && !empty($_hl_excluded_params[1])) {

            Data::create_data($_hl_excluded_params[1]);

            $_hl_excluded_variables = $_hl_excluded_params[1];

            foreach ($_hl_excluded_variables as $_hl_excluded_key => $_hl_excluded_variable) {

                if (!is_numeric($_hl_excluded_key) && !is_numeric($_hl_excluded_key{0})) {

                    ${$_hl_excluded_key} = $_hl_excluded_variable;
                }
            }

        }


        if (isset($_hl_excluded_params["text"]) && gettype($_hl_excluded_params["text"]) == "string") {

            $this->hl_content_create = true;

            print $_hl_excluded_params["text"];

            return;

        } else if (isset($_hl_excluded_params[2]) && $_hl_excluded_params[2] == "views") {

            //  view(...)

            $_hl_excluded_file = str_replace("//", "/", HLEB_GLOBAL_DIRECTORY . "/resources/views/" . $_hl_excluded_params[0][0] . ".php");

            //Отображение шаблона

            $this->hl_content_create = true;

            if (is_readable($_hl_excluded_file)) {

                include "$_hl_excluded_file";

            }


            return;


        } else if (isset($_hl_excluded_params[2]) && $_hl_excluded_params[2] == "render") {

            // render(...)

            $this->hl_content_create = true;

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
        if ($this->hl_content_create) return null;

        //Вызов controller

        $arguments = $action[1] ?? [];

        $call = explode("@", $action[0]);

        $initiator = "App\Controllers\\" . trim($call[0], "\\");

        $method = $call[1] ?? "index";

        return (new $initiator())->{$method}(...$arguments);


    }

    function calculate_time($name)
    {
        $num = count($this->hl_debug_info["time"]) + 1;
        $this->hl_debug_info["time"][$num . " " . $name] = round((microtime(true) - HLEB_START), 4);
    }


}