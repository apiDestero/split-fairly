<?php

interface DatabaseInterface
{
    public function execute($sql);
    public function query($sql);
}

interface QueryBuilderInterface
{
    public function insert(string $table, array $data): string;
    public function select(string $table, array $columns = ['*']): string;
    public function delete(string $table, array $conditions = []): string;
}

class MySQLDatabase implements DatabaseInterface
{
    private $conn;

    public function __construct($host, $user, $password, $database)
    {
        $this->conn = new mysqli($host, $user, $password, $database);

        if ($this->conn->connect_error)
        {
            throw new Exception("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function execute($sql)
    {
        return $this->conn->query($sql);
    }

    public function query($sql): array
    {
        $result = $this->conn->query($sql);

        $data = [];

        if ($result->num_rows > 0)
        {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}

class MySQLQueryBuilder implements QueryBuilderInterface
{
    public function insert(string $table, array $data): string
    {
        $columns = implode(', ', array_keys($data));
        $values = "'" . implode("', '", $data) . "'";
        return "INSERT INTO $table ($columns) VALUES ($values)";
    }

    public function select(string $table, array $columns = ['*']): string
    {
        $cols = implode(', ', $columns);
        return "SELECT $cols FROM $table";
    }

    public function delete(string $table, array $conditions = []): string
    {
        $where = '';
        if (!empty($conditions)) {
            $where = ' WHERE ';
            $where .= implode(' AND ', array_map(function ($key, $value) {
                if (is_array($value))
                    return "$key IN (".sprintf("'%s'", implode("','", $value)).")";
                else
                    return "$key = '$value'";
            }, array_keys($conditions), $conditions));
        }

        return "DELETE FROM $table" . $where;
    }
}

?>