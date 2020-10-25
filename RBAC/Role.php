<?php namespace codesaur\RBAC;

use codesaur\DataObject\CDO;

class Role
{
    public $permissions = array();

    public function getPermissions(CDO $connection, $role_id)
    {
        $permissions = new Permissions($connection);
        $role_perm = new RolePermission($connection);
        
        $table1 = $role_perm->getTable();
        $table2 = $permissions->getTable();
        
        $sql =  "SELECT t2.name, t2.alias FROM $table1 as t1 " .
                "JOIN $table2 as t2 ON t1.permission_id = t2.id " .
                'WHERE t1.role_id = :role_id AND t1.is_active = 1';
                
        $pdo_stmt = $role_perm->dataobject()->prepare($sql);
        $pdo_stmt->execute(array(':role_id' => $role_id));

        $this->permissions = array();
        if ($pdo_stmt->rowCount()) {
            while ($row = $pdo_stmt->fetch(\PDO::FETCH_ASSOC)) {
                $this->permissions["{$row['alias']}_{$row['name']}"] = true;
            }
        }
        
        return $this;
    }

    public function hasPermission(string $permissionName)
    {
        return isset($this->permissions[$permissionName]);
    }    
}
