<?php
header("Content-Type: application/json");
require_once "../../classes/HTTPResponse.class.php";
require_once "database/DB.class.php";
require_once "tblUsers.model.php";

// $MYSQL_conn = new DBSource(
//     DBSource::MYSQL_SERVER,
//     "192.168.1.164",
//     "portal_web",
//     "Hk80dlezi0f6",
//     "movesa_garantias",
//     3306
// );
// $MYSQL_db = DataBaseInstance::connect($MYSQL_conn);

// echo "<hr><h5>MSSQL</h5>";
// echo var_dump(
//     $MYSQL_db->query("SELECT * FROM PRESTAMO LIMIT 2")->headers()
// );

$MSSQL_conn = new DBSource(
    DBSource::MSSQL_SERVER,
    "192.168.1.3",
    "Consultor01",
    "Sql1sapphp@!",
    "MOVESA"
);

$conn = DataBaseInstance::connect($MSSQL_conn);
$users = new tblUsers($conn);

// $users->create([
//     tblUsers::NAME => "didier",
//     tblUsers::EMAIL => "programador@grupomovesa.com",
//     tblUsers::CREATED_AT => getdate()
// ]);

$users->update([
    tblUsers::NAME => "didier",
    tblUsers::EMAIL => "programador@grupomovesa.com",
    tblUsers::UPDATED_AT => getdate()
], tblUsers::ID, 0);

// echo "<h5>MSSQL</h5><hr>";
// echo json_encode($users->top(3));
