<?php

include_once "../config.php";
include_once ROOT_DIR . "/classes/service.class.php";

function createTable($data)
{
    return new HTTPResponse();
}

$service = new ServiceInterface();
$service->main();
