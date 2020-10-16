<?php namespace codesaur\RBAC;

use codesaur\Base\Base;
use codesaur\DataObject\MySQL;

class User extends Base
{
    protected $conn = null;
    protected $role = array();
    
    function __construct($conn = null)
    {
        if ($conn) {
            $this->conn = $conn;
        } else {
            $this->getConnection();
        }
    }
    
    public function init($user_id, string $alias)
    {
        if ( ! $this->hasConnection()) {
            return false;
        }
        
        $roles = new Roles($this->conn);
        $user_role = new UserRole($this->conn);
        
        $table1 = $user_role->getTable();
        $table2 = $roles->getTable();
        
        $organization_alias = '(t2.alias = :alias';
        if ($alias != 'system') {
            $organization_alias .= " OR t2.alias = 'system')";
        } else {
            $organization_alias .= ')';
        }
                
        $sql =  'SELECT t1.role_id, t2.name, t2.alias ' .
                "FROM $table1 as t1 JOIN $table2 as t2 ON t1.role_id = t2.id " .
                "WHERE $organization_alias AND t1.user_id = :user_id AND t1.is_active = 1";

        $pdo_stmt = $this->conn->prepare($sql);
        $pdo_stmt->execute(array(':user_id' => $user_id, ':alias' => $alias));
        
        $this->role = array();
        if ($pdo_stmt->rowCount()) {
            while ($row = $pdo_stmt->fetch(\PDO::FETCH_ASSOC)) {
                $this->role["{$row['alias']}_{$row['name']}"] = (new Role())->getPermissions($row['role_id'], $this->conn);
            }
        }
        
        return true;
    }

    public function hasRole(string $roleName)
    {
        foreach (\array_keys($this->role) as $name) {
            if ($name == $roleName) {
                return true;
            }
        }
        
        return false;
    }

    public function hasPrivilege(string $perm, $roleName = null)
    {
        if (isset($roleName)) {
            if (isset($this->role[$roleName])) {
                return $this->role[$roleName]->hasPermission($perm);
            } else {
                return false;
            }
        }
        
        foreach ($this->role as $role) {
            if ($role->hasPermission($perm)) {
                return true;
            }
        }
        
        return false;
    }
    
    public function hasConnection()
    {
        if ( ! $this->conn instanceof MySQL) {
            return false;
        }
        
        return $this->conn->alive();
    }
    
    public function getConnection() : \PDO
    {
        if ( ! $this->hasConnection()) {
            $this->conn = new MySQL(array(
                'driver'    => \getenv('DB_DRIVER') ?: 'mysql',
                'host'      => \getenv('DB_HOST') ?: 'localhost',
                'username'  => \getenv('DB_USERNAME') ?: 'root',
                'password'  => \getenv('DB_PASSWORD') ?: '',
                'name'      => \getenv('DB_NAME') ?: 'indoraptor',
                'engine'    => \getenv('DB_ENGINE') ?: 'InnoDB',
                'charset'   => \getenv('DB_CHARSET') ?: 'utf8',
                'collation' => \getenv('DB_COLLATION') ?: 'utf8_unicode_ci',
                'options'   => array(
                    \PDO::ATTR_PERSISTENT  => \getenv('DB_PERSISTENT') == 'true',
                    \PDO::ATTR_ERRMODE     => DEBUG ?
                    \PDO::ERRMODE_EXCEPTION : \PDO::ERRMODE_WARNING
                )
            ));

            if ($this->conn->alive()
                    && \getenv('TIME_ZONE_UTC')) {
                $this->conn->exec('SET time_zone = '
                        . $this->conn->quote(\getenv('TIME_ZONE_UTC')));
            }
        }
        
        return $this->conn;
    }
}
