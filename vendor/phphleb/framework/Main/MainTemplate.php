<?php

namespace Hleb\Main;

class MainTemplate
{
    function __construct(string $_hl_template_x_, array $_hl_template_params_x_ = [])
    {
        Info::insert("Templates", trim($_hl_template_x_, "/") . $this->hl_debug_backtrace());

        if(count($_hl_template_params_x_)) extract ($_hl_template_params_x_, EXTR_SKIP);

        $_hl_template_params_x_ = null;

        // Create HLEB Template.
        require HLEB_GLOBAL_DIRECTORY . "/resources/views/" . trim($_hl_template_x_, "/") . ".php";
    }

    function hl_debug_backtrace()
    {
        if(!HLEB_PROJECT_DEBUG) return "";
        $trace = debug_backtrace(2,4);
        if(isset($trace[3])){
            return  " (" . end(explode(HLEB_GLOBAL_DIRECTORY, $trace[3]["file"] ?? "")) . " : " . ($trace[3]["line"] ?? "") . ")";
        }
        return "";
    }

}


