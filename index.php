<?php
include_once "config.php";
include_once "classes/router.class.php";

$router = new routerClass();
$moduleData = $router->getModule();
//echo var_dump($moduleData);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="utilities.css">
    <link rel="stylesheet" href="estilos.css">
    <?php $moduleData->renderCssTags(); ?>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.js"></script>
    <script src="dist/utilities.js"></script>
    <script src="dist/menuBar.min.js"></script>
    <?php $moduleData->renderJsTags(); ?>
</head>

<body>
    <div class="ui teal inverted menu" id="top-tool-bar">
        <a class="header item" href="index.php">Proyecto Gestor BD's</a>
        <div class="ui dropdown item">
            <i class="plus icon"></i> Nuevo
            <div class="menu">
                <a class="icon item" href="index.php?router=table&action=create"><i class="table icon"></i> Tabla</a>
                <a class="icon item" href="#"><i class="unsplash icon"></i> Procedimiento Almacenado</a>
                <a class="icon item" href="#"><i class="fa-solid fa-code-branch icon"></i> Funcion</a>
                <a class="icon item" href="#"><i class="fa-regular fa-window-restore icon"></i> Vista</a>
                <a class="icon item" href="#"><i class="fa-solid fa-file-code icon"></i> Hoja de Consulta</a>
            </div>
        </div>
        <div class="ui dropdown item">
            <i class="eye icon"></i> Vista
            <div class="menu">
                <a class="icon item btn-toggle-menu" data-toggle="1">
                    <i class="compress arrows alternate icon"></i> Minimizar todo</a>
                <a class="icon item btn-toggle-menu" data-toggle="0">
                    <i class="compress icon"></i> Maximizar Todo
                </a>
            </div>
        </div>
    </div>

    <div class="ui fluid container px-4">
        <div class="ui grid two column celled">
            <div class="column five wide">
                <div class="ui header dividing">
                    <i class="server icon"></i>
                    <div class="content">Servidor: Microsoft SQL Server</div>
                </div>
                <div class="ui list" id="frm-server-objects-menu"></div>
                <div class="ui divider"></div>
            </div>

            <div class="column eleven wide">
                <?php $moduleData->renderHTML(); ?>
            </div>
        </div>
    </div>

</body>

</html>