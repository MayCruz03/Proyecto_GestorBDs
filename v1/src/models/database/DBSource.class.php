<?php

class DBSource
{
    const MSSQL_SERVER = "MSSQL";
    const MYSQL_SERVER = "MYSQL";

    public $db_server_type;
    public $db_server;
    public $db_user;
    public $db_pass;
    public $db_name;
    public $db_port;

    public function __construct($db_server_type, $db_server, $db_user, $db_pass, $db_name, $db_port = null)
    {
        $this->db_server_type = $db_server_type;
        $this->db_server = $db_server;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
        $this->db_name = $db_name;
        $this->db_port = $db_port;
    }

    public static function convert_utf8($str)
    {
        return utf8_encode($str);
    }

    public static function map_object_utf8($object)
    {
        $response = [];
        foreach ($object as $key => $value) {
            $key = self::convert_utf8($key);
            if (is_object($value) || is_array($value)) {
                $response[$key] = self::map_object_utf8($value);
            } else {
                $response[$key] = self::convert_utf8($value);
            }
        }

        return $response;
    }
}
