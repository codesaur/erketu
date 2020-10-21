<?php namespace codesaur\RBAC;

use codesaur\Base\Base;
use codesaur\DataObject\CDO;
use codesaur\Base\UserInterface;

class User extends Base implements UserInterface
{
    protected $conn = null; 
    protected $role = array();
    
    function __construct(CDO $connection)
    {
        $this->conn = $connection;
    }    
    
    public function init($user_id, string $alias)
    {
        if ( ! $this->isConnected()) {
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

    public function hasPrivilege(string $permissionName, $roleName = null)
    {
        if (isset($roleName)) {
            if (isset($this->role[$roleName])) {
                return $this->role[$roleName]->hasPermission($permissionName);
            } else {
                return false;
            }
        }
        
        foreach ($this->role as $role) {
            if ($role->hasPermission($permissionName)) {
                return true;
            }
        }
        
        return false;
    }
    
    public function isConnected()
    {
        if ( ! $this->conn instanceof CDO) {
            return false;
        }
        
        return $this->conn->alive();
    }
}
