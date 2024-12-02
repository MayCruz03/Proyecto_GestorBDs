<div class="ui dividing header">Datos de Tabla</div>

<table class="ui tiny table">
    <thead>
        <tr>
            <th class="two wide text-center">Tabla</th>
            <th class="three wide">
                <div class="ui input fluid">
                    <input type="text" placeholder="Esquema..." id="txt-schema-name"
                        value="<?= $_SESSION["DB_USER_SCHEMA"] ?? "No Definido" ?>" disabled>
                </div>
            </th>
            <th>
                <div class="ui input fluid">
                    <input type="text" placeholder="Nombre de Tabla..." id="txt-table-name">
                </div>
            </th>
        </tr>
    </thead>
</table>

<div class="ui dividing header">Estructura de Tabla</div>

<div class="ui very long scrolling container">
    <table class="ui head stuck unstackable celled tiny fluid table" id="tbl-columns">
        <thead>
            <tr class="text-center">
                <th colspan="5">Estructura</th>
                <th colspan="2">Auto-Incremental</th>
                <th colspan="2">Relacion</th>
            </tr>
            <tr class="text-center">
                <th data-content="Primary Key">PK</th>
                <th data-content="Unique">UQ</th>
                <th>Nombre Columna</th>
                <th>Tipo Dato</th>
                <th data-content="El campo admitira nulo?">NULL</th>
                <th data-content="Campo Auto-Incremental: desde <semilla> en salto de <incremental>">Auto-Inc.</th>
                <th class="two wide" data-content="Empezara a contar desde <semilla>, ejem. 1,20,3000...">Semilla</th>
                <th>Tabla</th>
                <th>Columna</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<table class="ui tiny table">
    <thead>
        <tr>
            <th class="two wide text-center">Agregar filas</th>
            <th class="four wide">
                <div class="ui input fluid">
                    <input type="number" placeholder="Cantidad filas..." id="txt-number-rows" value="1">
                </div>
            </th>
            <th>
                <div class="ui blue button" id="btn-add-rows"><i class="plus icon"></i>Agregar filas</div>
            </th>
        </tr>
    </thead>
</table>
<hr>

<div class="ui buttons" id="grd-action-buttons">
    <button class="ui green button" id="btn-create-table">Guardar</button>
    <a class="ui button" href="/">Cancelar</a>
</div>

<?php
$jsScripts = [
    "/js/createTable.js"
];
?>