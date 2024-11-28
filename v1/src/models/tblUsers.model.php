<?php

class tblUsers
{

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

    public static function getColumns()
    {
        $reflection = new ReflectionClass(self::class);
        return $reflection->getConstants();
    }

    public function all()
    {
        return $this->db->query("SELECT * FROM PT_VW_USUARIOS")->all();
    }
    public function find($column, $value)
    {
        return $this->db->query("SELECT * FROM PT_VW_USUARIOS WHERE {$column}  = ?", [$value])->first();
    }
}
