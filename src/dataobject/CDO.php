<?php

namespace codesaur\DataObject;

use PDO;
use Exception;

class CDO extends PDO
{
    private $_config;
    private $_connected = false;
    
    function __construct(array $config)
    {
        parent::__construct(
                "{$config['driver']}:host={$config['host']};charset={$config['charset']}",
                $config['username'], $config['password'], $config['options']
        );
                
        $this->_config = $config;
        $this->_connected = true;
    }
    
    public function alive(): bool
    {
        return $this->_connected;
    }
    
    public function getConfig(): array
    {
        return $this->_config;
    }

    public function getCharset(): ?string
    {
        return $this->getConfig()['charset'];
    }

    public function getCollation(): ?string
    {
        return $this->getConfig()['collation'];
    }

    public function getDriver(): ?string
    {
        return $this->getConfig()['driver'];
    }

    public function getEngine(): ?string
    {
        return $this->getConfig()['engine'];
    }

    public function getHost(): ?string
    {
        return $this->getConfig()['host'];
    }

    public function getDB(): ?string
    {
        return $this->getConfig()['dbname'];
    }

    public function useDB(?string $db = null)
    {
        if (empty($db)) {
            if (empty($this->getDB())) {
                throw new Exception('Must provide a database name!');
            }
            
            $db = $this->getDB();
        }
        
        if ($this->exec("USE $db") !== false) {
            $this->_config['dbname'] = $db;
            
            return true;
        }
        
        return false;
    }
    
    public function describe(string $table, $fetch_style = PDO::FETCH_ASSOC): array
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
    
    public function status(string $table, $fetch_style = PDO::FETCH_ASSOC)
    {
        $result = $this->query('SHOW TABLE STATUS LIKE ' . $this->quote($table));
        
        return $result->fetch($fetch_style);
    }
}
