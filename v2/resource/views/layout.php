<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Mi Aplicacion' ?></title>

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/css/utilities.css">
    <link rel="stylesheet" href="/css/app.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css"> -->


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.9/jquery.inputmask.min.js"></script>

    <script src="/js/app.js"></script>
    <script src="/js/layout.js"></script>

<body>
    <div class="ui teal inverted menu" id="top-tool-bar">
        <a class="header item" href="index.php">Proyecto Gestor BD's</a>
        <div class="ui dropdown item">
            <i class="plus icon"></i> Nuevo
            <div class="menu">
                <a class="icon item" href="/table/create"><i class="table icon"></i> Tabla</a>
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

        <a class="ui right item" href="/logout"><i class="power off icon"></i></a>
    </div>

    <div class="ui fluid container px-4">
        <div class="ui grid two column celled">
            <div class="column five wide">
                <div class="ui header dividing">
                    <i class="server icon"></i>
                    <div class="content">Servidor: <?= $_SESSION["DB_SERVER_NAME"] ?? "(No Definido)" ?></div>
                </div>
                <div class="ui list" id="frm-server-objects-menu"></div>
                <div class="ui divider"></div>
            </div>

            <main class="column eleven wide">
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>

    <?php
    foreach ($jsScripts ?? [] as $script) {
        echo "<script src='{$script}'></script>";
    }
    ?>
</body>

</html>