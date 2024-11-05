<?php

class controller
{
    private $conn;
    private $RoutersDir;

    function __construct($conection)
    {
        $this->conn = $conection;
        $this->RoutersDir = "routers";
    }

    public function main_access()
    {
        $response = $this->not_found_404();

        $router = $_GET["router"] ?? $_POST["router"] ?? "acceso";
        $action = $_GET["action"] ?? $_POST["action"] ?? "index";

        try {
            // si existe el controllador lo llama
            if (is_file("{$this->RoutersDir}/{$router}.router.php")) {

                require_once("{$this->RoutersDir}/{$router}.router.php");

                // si la funcion existe y es valida la ejecuta
                if (is_callable(["{$router}Router", $action]) == true) {

                    $routerClassObject = "{$router}Router";
                    $routerClassObject = new $routerClassObject();
                    $response = $routerClassObject->$action();

                    //$authorized = $this->validate_module($controller, $action, $_SESSION["Position"]);
                    $public_access = in_array($action, $controller_class->public_access ?? []);

                    //if ($public_access == false && $authorized == false) {
                    if ($public_access == false && $authorized == false) {
                        $response = $this->not_authorized_401();
                    }
                }
            }
        } catch (\Throwable $th) {
            $response = $this->error_500();
            echo $th->getMessage();
        }


        return $response;
    }

    function not_found_404()
    {

        return [
            "html" => "404.html",
            "js" => [],
            "css" => []
        ];
    }

    function error_500()
    {

        return [
            "html" => "500.html",
            "js" => [],
            "css" => []
        ];
    }

    function not_authorized_401()
    {

        return [
            "html" => "401.html",
            "js" => [],
            "css" => []
        ];
    }

    function enpoint_firma_cliente()
    {

        return [
            "html" => "enpoint_firma_cliente.php",
            "js" => "",
            "css" => ""
        ];
    }

    function validate_module($controller, $action, $rol)
    {

        $sql =
            "SELECT * 
            FROM [MOVESAWEB]..tbl_restricciones as t0
            INNER JOIN [MOVESAWEB]..TBL_PANTALLAS as t1 on t1.PAN_ID = t0.pan_id
            WHERE t1.PAN_CONTROLLER = ? AND t1.PAN_ACTION = ? AND t0.res_position = upper(?)";
        $stmt = sqlsrv_query($this->conn, $sql, array($controller, $action, $rol));
        return sqlsrv_has_rows($stmt);
    }
}
