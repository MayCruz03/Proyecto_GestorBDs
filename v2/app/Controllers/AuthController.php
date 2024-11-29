<?php

namespace App\Controllers;

use Lib\ApiResponse;

class AuthController extends Controller
{
    public function __construct()
    {
    }

    public function login()
    {
        return $this->view("auth.login");
    }

    public function main()
    {
        return $this->view("auth.main");
    }
}
