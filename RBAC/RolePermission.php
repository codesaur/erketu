<?php namespace codesaur\RBAC;

use codesaur\DataObject\CDO;
use codesaur\DataObject\Model;

class RolePermission extends Model
{
    function __construct(CDO $conn)
    {
        parent::__construct($conn);
        
        $this->structure(new RolePermissionDescribe());
        
        $this->setTable('rbac_role_perm');
    }
}
