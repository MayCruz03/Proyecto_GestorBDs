<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.js"></script>
    <style>
        body {
            background-color: #DADADA;
        }

        body>.grid {
            height: 100%;
        }

        .image {
            margin-top: -100px;
        }

        .column {
            max-width: 450px;
        }
    </style>
</head>

<body>
    <div class="ui middle aligned center aligned grid">
        <div class="column">
            <h2 class="ui teal image header">

                <div class="content">
                    Proyecto Gestor BD's
                </div>
            </h2>
            <form class="ui large form initial">
                <div class="ui stacked segment">
                    <div class="field">
                        <div class="ui left labeled input">
                            <div class="ui basic icon label">
                                <i class="server icon"></i>
                            </div>
                            <select name="" id="" class="ui dropdown">
                                <option value="" selected>Elige un servidor</option>
                                <option value="MSSQL">Microsoft SQL Server</option>
                                <option value="MYSQL">MySQL Server</option>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui left labeled input">
                            <div class="ui basic icon label">
                                <i class="user icon"></i>
                            </div>
                            <input type="text" name="user" placeholder="Usuario">
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui left labeled input">
                            <div class="ui basic icon label">
                                <i class="lock icon"></i>
                            </div>
                            <input type="text" name="password" placeholder="Contraseña">
                        </div>
                    </div>
                    <a class="ui fluid large teal submit button" href="index.php">Ingresar</a>
                </div>

                <div class="ui error message"></div>

            </form>

            <div class="ui message">
                ----
            </div>
        </div>
    </div>
</body>

</html>