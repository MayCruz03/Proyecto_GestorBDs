<?php
header("Content-Type: application/json");
require_once "DB.class.php";

$MYSQL_conn = new DBSource(
    DBSource::MYSQL_SERVER,
    "192.168.1.164",
    "portal_web",
    "Hk80dlezi0f6",
    "movesa_garantias",
    3306
);

$MSSQL_conn = new DBSource(
    DBSource::MSSQL_SERVER,
    "192.168.1.3",
    "Consultor01",
    "Sql1sapphp@!",
    "MOVESA"
);

$MYSQL_db = DataBaseInstance::connect($MYSQL_conn);
$MSSQL_db = DataBaseInstance::connect($MSSQL_conn);

echo "<h5>MSSQL</h5><hr>";
echo var_dump(
    $MSSQL_db->query("SELECT * FROM PT_VW_USUARIOS")->headers()
);

echo "<hr><h5>MSSQL</h5>";
echo var_dump(
    $MYSQL_db->query("SELECT * FROM PRESTAMO LIMIT 2")->headers()
);
