<?php
session_start();
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
date_default_timezone_set('America/Tegucigalpa');

include_once "HTTPResponse.class.php";
include_once "HTTPRequest.class.php";

class ServiceInterface
{

    /**
     * Usada para inicializar un archivo de funciones php en el cual mediante [POST] permite desde [JS] ejecutar un callbak de [PHP]
     * Ejemplo de json que se debe enviar desde JS: { request: "nombre_de_funcion_php", data: {...} }
     * La funcion llamada en el argumento [request] debe estar ubicada dentro del mismo archivo .php en donde se llama la funcion \ServiceInterface->main()
     * Retorna los datos devueltos dentro del callback de [PHP] invocado usando json_encode()
     */
    public function main()
    {
        $allowedMethod = in_array($_SERVER["REQUEST_METHOD"], ["POST", "GET"]);
        if ($allowedMethod) { // metodo [POST] es el unico permitido

            if (!isset($_SESSION["IdUnico"])) {
                $response = new HTTPResponse();
                $response->Forbidden("Expired Session or API Token is Invalid  - 401 Forbidden");
            } else {

                $request = $_REQUEST["data"] ?? $_POST["request"] ?? $_GET["request"] ?? null;
                if (empty($request)) {
                    $response = $this->_default();
                } else {
                    $data = $_REQUEST["data"] ?? $_POST["data"] ?? $_GET["data"] ?? [];
                    $response = call_user_func($request, $data);
                }
            }
        } else {
            $response = new HTTPResponse();
            $response->MethodNotAllowed("Metodo [POST][GET] esperado - 405 Method Not Allowed");
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    /**
     * En caso de no enviarse el parametro [request] dentro del [POST] devuelve un default
     */
    public function _default()
    {
        $response = new HTTPResponse();
        $response->NotFound(404, "'request' parameter expected");

        return $response;
    }
}
