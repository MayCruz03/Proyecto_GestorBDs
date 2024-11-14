<?php

class tblUsers
{

    // definir columnas
    const ID = 'id';
    const NAME = 'name';
    const EMAIL = 'email';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * @var DBClassTemplate
     */
    private $db;

    public function __construct(DBClassTemplate $db)
    {
        $this->db = $db;
    }

    public static function getColumns($onlyNames = false)
    {
        $reflection = new ReflectionClass(self::class);
        return $reflection->getConstants();
    }

    public function all()
    {
        return $this->db->query("SELECT * FROM PT_VW_USUARIOS")->all();
    }

    public function where($column, $value)
    {
        return $this->db->query("SELECT * FROM PT_VW_USUARIOS WHERE {$column}  = ?", [$value])->all();
    }

    public function top(int $topNumber = 100)
    {
        $sqlQueries = [
            DBSource::MSSQL_SERVER => "SELECT TOP {$topNumber} * FROM PT_VW_USUARIOS",
            DBSource::MYSQL_SERVER => "SELECT * FROM PT_VW_USUARIOS LIMIT {$topNumber}",
        ];
        $sql = $sqlQueries[$this->db->type()] ?? "SELECT * FROM PT_VW_USUARIOS";

        return $this->db->query($sql)->all();
    }

    public function create($parameters)
    {
        $columns = implode("],[", array_keys($parameters));
        $columns = "[{$columns}]";

        $values = array_values($parameters);

        $inyectedValues = str_repeat("?, ", count($values));
        $inyectedValues = substr($inyectedValues, 0, -2);

        $sql = "INSERT INTO MOVESAWEB..TABLE_USER_MOTO ({$columns}) VALUES ({$inyectedValues})";
        $status = $this->db->query($sql, $values)->status();
        $id = $this->db->lastInsertId();

        //return $sql;
        return new HTTPResponse(
            200,
            $this->db->error(),
            $status,
            $this->where(tblUsers::ID, $id)
        );
    }

    public function update($parameters, $columnCondition, $valueCondition)
    {

        $columns = array_map(function ($key) {
            return "{$key} = ?";
        }, array_keys($parameters));
        $columns = implode(", ", $columns);

        $values = array_values($parameters);
        $values[] = $valueCondition;

        $sql = "UPDATE MOVESAWEB..TABLE_USER_MOTO SET {$columns} WHERE {$columnCondition} = ?";
        $status = $this->db->query($sql, $values)->status();

        return new HTTPResponse(
            200,
            $this->db->error(),
            $status,
            $this->where($columnCondition, $valueCondition)
        );
    }
}
