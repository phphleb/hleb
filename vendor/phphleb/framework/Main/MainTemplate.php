<?php

namespace Hleb\Main;

class MainTemplate
{
    function __construct(string $_hl_template_x_, array $_hl_template_params_x_)
    {
        Info::insert("Templates", trim($_hl_template_x_, "/"));

        if(count($_hl_template_params_x_)) extract ($_hl_template_params_x_, EXTR_SKIP);

        // Create HLEB Template.
        require HLEB_GLOBAL_DIRECTORY . "/resources/views/" . trim($_hl_template_x_, "/") . ".php";
    }

}