<?php

namespace App\Controllers;

use lib\ApiResponse;
use Lib\HttpClient;

class Controller
{
    const CHECK_EMPTY_REGULAR = 0;
    const CHECK_EMPTY_STRICT = 1;

    protected $Api;
    protected $publicAccess = [];

    public function __construct()
    {
        $this->Api = new HttpClient(API_DOMAIN);

        $this->Api->setHeaders([
            //'Authorization' => 'Bearer your_access_token',
            'Accept' => 'application/json',
        ]);
    }

    public function verifySession()
    {
        $auth = $this->Api->get("/validateAuthentication");
        return $auth;
    }

    public function view($route, $data = [], $useLayout = true)
    {
        extract($data);

        $route = str_replace(".", "/", $route);
        $viewFile = VIEWS_DIR . "/{$route}.php";

        if (file_exists($viewFile)) {
            ob_start();
            include $viewFile;
            $content = ob_get_clean();

            // Cargar el layout principal
            $layoutFile = VIEWS_DIR . "/layout.php";
            if ($useLayout && file_exists($layoutFile)) {
                ob_start();
                include $layoutFile;
                return ob_get_clean();
            }

            return $content; // Fallback en caso de que el layout no exista
        } else {
            return "El archivo de vista no existe: {$viewFile}";
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

    public function checkRequestParams($requiredFields, $checkEmpty = false, $flags = self::CHECK_EMPTY_REGULAR)
    {
        $response = new ApiResponse();

        $requestData = $this->getPostData();
        $requestDataKeys = array_keys($requestData);

        foreach ($requiredFields as $field) {
            if (!in_array($field, $requestDataKeys)) {
                return $response->Error(500, "Se esperaba recibir el parametro [{$field}], no fue recibido");
            } else if ($checkEmpty && $this->empty($requestData[$field], $flags)) {
                return $response->Error(500, "El parametro [{$field}] es obligatorio, no puede estar vacio... ");
            }
        }

        $response->Ok($requestData);
        return $response;
    }

    public function empty($value, $flags)
    {
        if (is_array($value))
            return empty($value);

        $value = trim($value);

        switch ($flags) {
            case self::CHECK_EMPTY_REGULAR:
                $empty = in_array($value, [null, ""]);
                break;
            case self::CHECK_EMPTY_STRICT:
                $empty = in_array($value, [null, "", 0]);
                break;

            default:
                $empty = in_array($value, [null, ""]);
                break;
        }

        return $empty;
    }

    public function isPublicAccess($item)
    {
        return in_array($item, $this->publicAccess);
    }
}
