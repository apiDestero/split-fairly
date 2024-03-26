<?php

class UserRepository
{
    private $db;
    private $queryBuilder;

    public function __construct(DatabaseInterface $db, QueryBuilderInterface $queryBuilder)
    {
        $this->db = $db;
        $this->queryBuilder = $queryBuilder;
    }

    public function save(User $user): bool
    {
        $data = [
            'name' => $user->getName(),
            'email' => $user->getEmail()
        ];

        $query = $this->queryBuilder->insert('users', $data);
        return $this->db->execute($query);
    }

    public function getAll(): array
    {
        $query = $this->queryBuilder->select('users');
        return $this->db->query($query);
    }

    public function deleteAll(): array
    {
        $query = $this->queryBuilder->delete('users');
        return $this->db->query($query);
    }
}

?>