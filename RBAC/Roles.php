<?php namespace codesaur\RBAC;

use codesaur\DataObject\CDO;
use codesaur\MultiModel\InitableModel;

class Roles extends InitableModel
{
    function __construct(CDO $conn)
    {
        parent::__construct($conn);
        
        $this->structure(new RolesDescribe());
        
        $this->setTable('rbac_roles');
    }
    
    public function initial() : bool
    {
        $table = $this->getTable();        
        if ( ! parent::initial() &&
                $table == 'rbac_roles') {
            $nowdate = \date('Y-m-d H:i:s');
            $sql =  "INSERT INTO $table (id,created_at,name,description,alias) " .
                    "VALUES (1,'$nowdate','coder','Coder can do anything!','system')";

            if ($this->dataobject()->exec($sql) === false) {
                return false;
            }
        }
        
        return true;
    }
}
