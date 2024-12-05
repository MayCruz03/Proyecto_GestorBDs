<div class="ui form">
    <div class="four fields">
        <div class="field">
            <select id="cbb-tables" class="ui dropdown fluid">
                <option value="">Selecciona una Tabla</option>
                <?php
                foreach ($tables as $item) {
                    $tableName = "{$item["OBJ_SCHEMA_NAME"]}.{$item["OBJ_NAME"]}";
                    echo "<option value='{$item["OBJ_ID"]}' data-table='{$tableName}'>{$tableName}</option>";
                }
                ?>
            </select>
        </div>
        <div class="field">
            <div class="ui tiny buttons">
                <button class="ui button btn-generate-command" data-action="SELECT">SELECT</button>
                <button class="ui button btn-generate-command" data-action="INSERT">INSERT</button>
                <button class="ui button btn-generate-command" data-action="UPDATE">UPDATE</button>
                <button class="ui button btn-generate-command" data-action="DELETE">DELETE</button>
            </div>
        </div>
    </div>
</div>

<div id="txt-code-editor" style="height: 300px; width: 100%; border: 1px solid #ccc;"></div>
<br>

<div class="ui tiny buttons">
    <button class="ui blue icon button" id="btn-execute-code">Ejecutar Código <i class="play icon"></i></button>
    <button class="ui icon button" id="btn-clear-editor">Limpiar Editor<i class="broom icon"></i></button>
</div>

<div class="ui divider"></div>

<div class="ui top attached tabular menu">
    <a class="item" data-tab="frm-output-log">Consola</a>
    <a class="item" data-tab="frm-fetching-rows">Resultados</a>
</div>
<div class="ui bottom attached tab segment" data-tab="frm-output-log">

    <div class="ui tiny buttons">
        <button class="ui icon button" id="btn-clear-log">Limpiar Consola<i class="broom icon"></i></button>
    </div>

    <table class="ui fixed single line celled tiny table" id="tbl-output-log">
        <thead>
            <tr>
                <th class="one wide"></th>
                <th class="two wide">Hora</th>
                <th class="five wide">Comando SQL</th>
                <th class="six wide">Mensaje</th>
                <th class="two wide">Duracion</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="ui bottom attached tab segment" data-tab="frm-fetching-rows"></div>

<?php
$jsScripts = [
    "/js/queryManager.js"
];
?>