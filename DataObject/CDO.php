<?php namespace codesaur\DataObject;

class CDO extends \PDO
{
    private $_config;
    private $_connected;
    
    function __construct(array $config)
    {
        try {
            parent::__construct(
                    "{$config['driver']}:host={$config['host']}" .
                    ";dbname={$config['name']};charset={$config['charset']}",
                    $config['username'], $config['password'], $config['options']
            );
            $this->_connected = true;
            $this->_config = $config;
        } catch (\PDOException $e) {
            if (DEBUG) {
                \error_log($e->getMessage());
            }
            
            $this->_connected = false;
        }
    }
    
    public function alive() : bool
    {
        return $this->_connected;
    }
    
    public function getConfig() : array
    {
        return $this->_config;
    }

    public function getCharset() : string
    {
        return $this->getConfig()['charset'];
    }

    public function getCollation() : string
    {
        return $this->getConfig()['collation'];
    }

    public function getDB() : string
    {
        return $this->getConfig()['name'];
    }
    
    public function getDriver() : string
    {
        return $this->getConfig()['driver'];
    }

    public function getEngine() : string
    {
        return $this->getConfig()['engine'];
    }

    public function getHost() : string
    {
        return $this->getConfig()['host'];
    }

    public function selectDB(string $db)
    {
        return $this->exec("USE $db") !== false;
    }
}
