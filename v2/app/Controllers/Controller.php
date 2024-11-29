<?php

namespace App\Controllers;

use lib\ApiResponse;

class Controller
{
    protected $publicAccess = [];

    public function view($route, $data = [])
    {
        extract($data);

        $route = str_replace(".", "/", $route);
        $URLview = VIEWS_DIR . "/{$route}.php";

        if (file_exists($URLview)) {
            ob_start();
            include $URLview;
            $content = ob_get_clean();

            return $content;
        } else {
            return "El archivo no existe";
        }
    }

    public function getPostData(): array
    {
        $parameters = [];

        $rawInput = file_get_contents('php://input');
        $decodedJson = json_decode($rawInput, true);

        if (is_array($decodedJson)) {
            $parameters = array_merge($parameters, $decodedJson);
        }

        if (!empty($_POST)) {
            $parameters = array_merge($parameters, $_POST);
        }

        return $parameters;
    }

    public function checkRequestParams($requiredFields)
    {
        $response = new ApiResponse();

        $requestData = $this->getPostData();
        $requestDataKeys = array_keys($requestData);

        foreach ($requiredFields as $field) {
            if (!in_array($field, $requestDataKeys)) {
                $response->Error(500, "Se esperaba recibir el parametro [{$field}], no fue recibido");
                return $response;
            }
        }

        $response->Ok($requestData);
        return $response;
    }
}
