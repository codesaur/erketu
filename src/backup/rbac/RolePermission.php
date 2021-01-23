<?php namespace codesaur\Backup\RBAC;

use codesaur\Backup\DataObject\CDO;
use codesaur\Backup\DataObject\Model;

class RolePermission extends Model
{
    function __construct(CDO $conn)
    {
        parent::__construct($conn);
        
        $this->structure(new RolePermissionDescribe());
        
        $this->setTable('rbac_role_perm');
    }
}
