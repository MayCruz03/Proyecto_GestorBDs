<?php
require_once "DBSource.class.php";
require_once "DBClassTemplate.class.php";

class MYSQL extends DBClassTemplate
{
    protected $conn;
    protected $connResource;
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
        $this->conn = new mysqli(
            $conn->db_server,
            $conn->db_user,
            $conn->db_pass,
            $conn->db_name,
            $conn->db_port
        );

        if ($this->conn->connect_error) {
            die("MYSQL CONNECTION FAILED: {$this->conn->connect_error}");
        }
    }

    public function query($sqlQuery, $parameters = []): DBClassTemplate
    {
        $stmt = $this->conn->prepare($sqlQuery);

        if (!$stmt) {
            $this->lastQueryStatus = false;
            $this->lastError = $this->conn->error;
            return $this;
        }

        if (count($parameters) > 0) {
            $stmt->bind_param(str_repeat('s', count($parameters)), ...$parameters);
        }

        $this->lastQueryStatus = $stmt->execute();
        if (!$this->lastQueryStatus) {
            $this->lastError = $stmt->error;
        } else {
            $this->query = $stmt->get_result();
            $this->lastError = null;
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
        $stmt = $this->conn->query("SELECT @@IDENTITY AS last_id");
        if ($stmt && $row = $stmt->fetch_assoc()) {
            return $row["last_id"];
        } else {
            return 0;
        }
    }

    public function beginTran(): void
    {
        $this->conn->begin_transaction();
    }

    public function finishTran($status): void
    {
        if ($status) {
            $this->conn->commit();
        } else {
            $this->conn->rollback();
        }
    }

    public function first(): array
    {
        return DBSource::map_object_utf8($this->query->fetch_assoc());
    }

    public function all(): array
    {
        return DBSource::map_object_utf8($this->query->fetch_all(MYSQLI_ASSOC));
    }

    public function headers(): array
    {
        $headers = $this->query->fetch_fields();
        return array_map(function ($item) {
            return [
                "name" => $item->name
            ];
        }, $headers);
    }
}
