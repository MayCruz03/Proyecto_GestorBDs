<?php

interface DBInterface
{
    public function connect(DBSource $conn);
    public function query($sqlQuery, $parameters = []);
    public function status();
    public function error();
    public function lastInsertId();
    public function beginTran();
    public function finishTran($status);
    public function first();
    public function all();
    public function headers();
}


abstract class DBClassTemplate implements DBInterface
{
    protected $conn;
    /**
     * @var DBSource
     */
    protected $connResource;
    protected $connStatus;
    protected $query;
    protected $lastQueryStatus;
    protected $lastError;

    abstract public function connect(DBSource $conn): void;
    abstract public function query($sqlQuery, $parameters = []): self;
    abstract public function status(): bool;
    abstract public function error(): string;
    abstract public function lastInsertId(): int;
    abstract public function beginTran(): void;
    abstract public function finishTran($status): void;
    abstract public function first(): array;
    abstract public function all(): array;
    abstract public function headers(): array;

    public function type()
    {
        return $this->connResource->db_server_type;
    }
}
