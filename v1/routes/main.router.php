<?php

class mainRouter
{
    public $public_access = [];

    public function index()
    {
        return new moduleData(
            "default.php",
        );
    }
}
