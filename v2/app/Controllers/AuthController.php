<?php

namespace App\Controllers;

use Lib\ApiResponse;

class AuthController extends Controller
{
    protected $publicAccess = ["getServers", "login", "logout", "loginPostBack"];

    public function __construct()
    {
        parent::__construct();
    }

    public function getServers()
    {

        $fileData = file_get_contents(DATA_DIR . "/servers.json");
        $servers = json_decode($fileData, true);
        return $servers;
    }

    public function login()
    {
        return $this->view("login", [
            "title" => "Inicio de sesion",
            "servers" => $this->getServers()
        ], false);
    }

    public function logout()
    {
        $request = $this->Api->post("/logout");

        if ($request->success) {
            setcookie(WEB_COOKIE_NAME, '', [
                'expires' => time() - 3600,  // Tiempo en el pasado
                'path' => '/',               // Debe coincidir con la configuración original
                'httponly' => true,          // Igual que al configurarla
                'samesite' => 'Strict'       // Igual que al configurarla
            ]);

            unset($_COOKIE[WEB_COOKIE_NAME]);
            header("location: /login");
        }

        return $request;
    }

    public function loginPostBack()
    {
        $response = new ApiResponse();

        $fields = ["server", "user", "password"];
        $request = $this->checkRequestParams($fields, true);
        if (!$request->success) {
            return $request;
        }

        $parameters = $request->data;

        $servers = $this->getServers();
        $serverSelectedData = array_filter($servers, function ($item) use ($parameters) {
            return $item["db_server_code"] == $parameters["server"];
        });

        if (count($serverSelectedData) == 0) {
            return $response->Error(500, "Servidor seleccionado no es valido o no ha sido encontrado");
        }

        $loginData = current($serverSelectedData);
        $loginData["db_user"] = $parameters["user"];
        $loginData["db_pass"] = $parameters["password"];

        $loginResponse = $this->Api->post("/login", $loginData);

        $exp = intval($loginResponse->data["duration"]);
        $token = $loginResponse->data["token"];
        $schema = $loginResponse->data["userSchema"];

        setcookie(WEB_COOKIE_NAME, $token, [
            'expires' => time() + $exp,
            'path' => '/',              // Disponible en todo el dominio
            'httponly' => true,         // Solo accesible desde HTTP (no JavaScript)
            'samesite' => 'Strict'      // Evita el envío en solicitudes de terceros
        ]);

        setcookie(WEB_COOKIE_NAME . '_EXP', time() + $exp, [
            'expires' => time() + $exp,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        $_SESSION["DB_SERVER_NAME"] = $loginData["db_server_alias"];
        $_SESSION["DB_NAME"] = $loginData["db_name"];
        $_SESSION["DB_USER_SCHEMA"] = $schema;

        return $loginResponse;
    }

    public function main()
    {
        return $this->view("main");
    }
}
