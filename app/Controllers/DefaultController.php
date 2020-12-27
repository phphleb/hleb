<?php

namespace App\Controllers;

class DefaultController extends \MainController
{
    public function index() {
        return view("default");
    }

}

