<?php
require_once "../config.php";

if (isset($_COOKIE[WEB_COOKIE_NAME])) {
    // Calcular el tiempo restante de la cookie
    $remainingTime = $_COOKIE[WEB_COOKIE_NAME] ? (intval($_COOKIE[WEB_COOKIE_NAME . '_EXP']) - time()) : 0;

    if ($remainingTime > 0) {
        // Sincronizar el tiempo de vida de la sesión con el de la cookie
        ini_set('session.cookie_lifetime', $remainingTime);
        ini_set('session.gc_maxlifetime', $remainingTime);
    }
}

session_start();
mb_internal_encoding("UTF-8");
mb_http_output("UTF-8");

require_once "../autoload.php"; // autoload de proyecto
//require_once "../vendor/autoload.php"; // autoload de librerias

use lib\Route;

require_once "../routes/WebRoute.php";

Route::dispatch();
