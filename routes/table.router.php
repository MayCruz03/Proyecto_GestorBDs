<?php

class tableRouter
{
    public $public_access = [];

    public function create()
    {
        return new moduleData(
            "table/createTable.php",
        );
    }
}
