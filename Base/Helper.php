<?php namespace codesaur\Base;

use codesaur\DataObject\MySQL;

class Helper extends Base
{
    public function getPDO() : MySQL
    {
        $configuration = array(
            'driver'    => \getenv('DB_DRIVER') ?: 'mysql',
            'host'      => \getenv('DB_HOST') ?: 'localhost',
            'username'  => \getenv('DB_USERNAME') ?: 'root',
            'password'  => \getenv('DB_PASSWORD') ?: '',
            'name'      => \getenv('DB_NAME') ?: 'indoraptor',
            'engine'    => \getenv('DB_ENGINE') ?: 'InnoDB',
            'charset'   => \getenv('DB_CHARSET') ?: 'utf8',
            'collation' => \getenv('DB_COLLATION') ?: 'utf8_unicode_ci',
            'options'   => array(
                \PDO::ATTR_ERRMODE     => DEBUG ?
                \PDO::ERRMODE_EXCEPTION : \PDO::ERRMODE_WARNING,
                \PDO::ATTR_PERSISTENT  => \getenv('DB_PERSISTENT') == 'true'
            )
        );
        
        $conn = new MySQL($configuration);
        
        if ($conn->alive()) {
            if (\getenv('TIME_ZONE_UTC')) {
                $conn->exec('SET time_zone = ' . $conn->quote(\getenv('TIME_ZONE_UTC')));
            }
        }
        
        return $conn;
    }
}
