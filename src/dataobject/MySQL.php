<?php namespace codesaur\DataObject;

class MySQL extends CDO
{
    public function describe(string $table, $fetch_style = \PDO::FETCH_ASSOC): array
    {
        $statement = $this->prepare("DESCRIBE $table");
        $statement->execute();
        
        return $statement->fetchAll($fetch_style);
    }

    public function has(string $table): bool
    {
        $results = $this->query('SHOW TABLES LIKE ' . $this->quote($table));
        
        return $results->rowCount() > 0;
    }
    
    public function status(string $table, $fetch_style = \PDO::FETCH_ASSOC)
    {
        $result = $this->query('SHOW TABLE STATUS LIKE ' . $this->quote($table));
        
        return $result->fetch($fetch_style);
    }
}
