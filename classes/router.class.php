<?php

class moduleData
{

    public $html;
    public $js;
    public $css;
    public $methodsAlowed;

    function __construct($html = "", $js = [], $css = [], $methodsAlowed = ["GET"])
    {
        $this->html = $html;
        $this->js = $js;
        $this->css = $css;
        $this->methodsAlowed = $methodsAlowed;
    }

    function renderHTML()
    {
        if (file_exists(VIEWS_DIR . "/{$this->html}")) {
            require_once VIEWS_DIR . "/{$this->html}";
        } else {
            require_once VIEWS_DIR . "/auth/404.html";
        }
    }

    function renderJsTags()
    {
        $scripts = "";
        if (empty($this->js)) return $scripts;

        if (is_array($this->js)) {
            foreach ($this->js as $scriptURL) {
                $scriptURL = JS_DIR . "/$scriptURL";
                $scripts .= "<script src='{$scriptURL}'></script>\n";
            }
            return $scripts;
        }

        $scripts = "<script src='{$this->js}'></script>";
        return $scripts;
    }

    function renderCssTags()
    {
        $styleLinks = "";
        if (empty($this->css)) return $styleLinks;

        if (is_array($this->css)) {
            foreach ($this->css as $linkURL) {
                $linkURL = CSS_DIR . "/$linkURL";
                $styleLinks .= "<link rel='stylesheet' href='{$linkURL}' />\n";
            }
            return $styleLinks;
        }

        $styleLinks = "<link rel='stylesheet' href='{$this->css}' />";
        return $styleLinks;
    }
}


class routerClass
{
    public function getModule(): moduleData
    {
        $response = $this->not_found_404();

        $router = $_GET["router"] ?? $_POST["router"] ?? "main";
        $action = $_GET["action"] ?? $_POST["action"] ?? "index";
        $method = $_SERVER["REQUEST_METHOD"];

        try {
            // si existe el controllador lo llama
            if (is_file(ROUTES_DIR . "/{$router}.router.php")) {

                require_once ROUTES_DIR . "/{$router}.router.php";

                // si la funcion existe y es valida la ejecuta
                if (is_callable(["{$router}Router", $action]) == true) {

                    $routerClassObject = "{$router}Router";
                    $routerClassObject = new $routerClassObject();

                    /**
                     * @var moduleData
                     */
                    $response = $routerClassObject->$action();

                    if (!in_array($method, $response->methodsAlowed)) {
                        return $this->not_authorized_401();
                    }

                    //$authorized = $this->validate_module($controller, $action, $_SESSION["Position"]);
                    //$public_access = in_array($action, $routerClassObject->public_access ?? []);

                    //if ($public_access == false && $authorized == false) {
                    // if ($public_access == false) {
                    //     $response = $this->not_authorized_401();
                    // }
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
        return new moduleData("auth/404.html");
    }

    function error_500()
    {
        return new moduleData("auth/500.html");
    }

    function not_authorized_401()
    {
        return new moduleData("auth/401.html");
    }
}
