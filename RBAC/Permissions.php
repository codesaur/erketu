<?php namespace codesaur\RBAC;

use codesaur\DataObject\CDO;
use codesaur\MultiModel\InitableModel;

class Permissions extends InitableModel
{
    function __construct(CDO $conn)
    {
        parent::__construct($conn);
        
        $this->structure(new PermissionsDescribe());
        
        $this->setTable('rbac_permissions');
    }
    
    public function initial() : bool
    {
        $table = $this->getTable();
        if ( ! parent::initial() &&
                $table == 'rbac_permissions') {
            // 
        }
        
        return true;
    }
}
