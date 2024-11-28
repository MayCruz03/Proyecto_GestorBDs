<?php

require_once "MYSQL.class.php";
require_once "MSSQL.class.php";

class DataBaseInstance
{
    public static function connect(DBSource $conn)
    {
        $db = null;
        if ($conn->db_server_type == DBSource::MSSQL_SERVER) {
            $db = new MSSQL($conn);
        } else if ($conn->db_server_type == DBSource::MYSQL_SERVER) {
            $db = new MYSQL($conn);
        }

        return $db;
    }
}
