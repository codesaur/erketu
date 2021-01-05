<?php namespace codesaur\RBAC;

use codesaur\DataObject\CDO;

class RBACUser implements \JsonSerializable
{
    public $role = array();
    
    public function __construct(CDO $connection, $user_id, string $alias)
    {
        if ( ! $connection->alive()) {
            throw new \Exception(__CLASS__ . ' can\'t init. Database not connected');
        }
        
        if (empty($user_id) || empty($alias)) {
            throw new \Exception(__CLASS__ . ' can\'t init. Missing information');
        }
        
        $roles = new Roles($connection);
        $user_role = new UserRole($connection);
        
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
                "WHERE t1.user_id = :user_id AND t1.is_active = 1";

        $pdo_stmt = $connection->prepare($sql);
        $pdo_stmt->execute(array(':user_id' => $user_id));
        
        $this->role = array();
        if ($pdo_stmt->rowCount()) {
            while ($row = $pdo_stmt->fetch(\PDO::FETCH_ASSOC)) {
                $this->role["{$row['alias']}_{$row['name']}"] = (new Role())->getPermissions($connection, $row['role_id']);
            }
        }
    }

    public function hasRole(string $roleName) : bool
    {
        foreach (\array_keys($this->role) as $name) {
            if ($name == $roleName) {
                return true;
            }
        }
        
        return false;
    }

    public function hasPrivilege(string $permissionName, $roleName = null) : bool
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
    
    public function jsonSerialize()
    {
        $role_permissions = array();
        
        foreach ($this->role as $name => $role) {
            if ( ! $role instanceof Role) {
                continue;
            }
            
            $role_permissions[$name] = array();
            
            foreach ($role->permissions as $permission => $granted) {
                $role_permissions[$name][$permission] = $granted;
            }
        }
        
        return $role_permissions;
    }
}
