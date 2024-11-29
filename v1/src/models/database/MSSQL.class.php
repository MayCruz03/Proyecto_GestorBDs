<?php
require_once "DBSource.class.php";
require_once "DBClassTemplate.class.php";

class MSSQL extends DBClassTemplate
{
    protected $connResource;
    protected $conn;
    protected $query;
    protected $lastQueryStatus;
    protected $lastError;

    public function __construct(DBSource $conn)
    {
        $this->connResource = $conn;
        $this->connect($this->connResource);
    }

    public function connect(DBSource $conn): void
    {
        $connectionOptions = [
            "Database" => $conn->db_name,
            "UID" => $conn->db_user,
            "PWD" => $conn->db_pass,
            "CharacterSet" => "UTF-8"
        ];

        $this->conn = sqlsrv_connect($conn->db_server, $connectionOptions);

        if (!$this->conn) {
            die("MSSQL CONNECTION FAILED: " . print_r(sqlsrv_errors(), true));
        }
    }

    public function query($sqlQuery, $parameters = []): DBClassTemplate
    {
        // Preparar la consulta
        $stmt = sqlsrv_prepare($this->conn, $sqlQuery, $parameters);

        if (!$stmt) {
            $this->lastQueryStatus = false;
            $this->lastError = sqlsrv_errors();
            return $this;
        }

        // Ejecutar la consulta
        $this->lastQueryStatus = sqlsrv_execute($stmt);

        if (!$this->lastQueryStatus) {
            $this->lastError = sqlsrv_errors();
            $this->query = null;
        } else {
            $this->lastError = null;
            $this->query = $stmt;
        }

        return $this;
    }

    public function status(): bool
    {
        return $this->lastQueryStatus;
    }

    public function error(): string
    {
        return $this->lastError;
    }

    public function lastInsertId(): int
    {
        // Ejecutar una consulta para obtener el último ID insertado en el contexto de la conexión actual
        $stmt = sqlsrv_query($this->conn, "SELECT SCOPE_IDENTITY() AS last_id");

        if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            return $row["last_id"];
        } else {
            return 0;
        }
    }

    public function beginTran(): void
    {
        sqlsrv_begin_transaction($this->conn);
    }

    public function finishTran($status): void
    {
        if ($status) {
            sqlsrv_commit($this->conn);
        } else {
            sqlsrv_rollback($this->conn);
        }
    }

    /**
     * @return array|null
     */
    public function first(): array
    {
        if ($this->query) {
            $row = sqlsrv_fetch_array($this->query, SQLSRV_FETCH_ASSOC);
            return DBSource::map_object_utf8($row);
        }
        return null;
    }

    public function all(): array
    {
        $results = [];
        if ($this->query) {
            while ($row = sqlsrv_fetch_array($this->query, SQLSRV_FETCH_ASSOC)) {
                $results[] = DBSource::map_object_utf8($row);
            }
        }
        return $results;
    }

    public function headers(): array
    {
        $headers = sqlsrv_field_metadata($this->query);
        return array_map(function ($item) {
            return [
                "name" => $item["Name"]
            ];
        }, $headers);
    }
}
