<?php namespace codesaur\RBAC;

use codesaur\DataObject\CDO;
use codesaur\MultiModel\InitableModel;

class UserRole extends InitableModel
{
    function __construct(CDO $conn)
    {
        parent::__construct($conn);
        
        $this->structure(new UserRoleDescribe());
        
        $this->setTable('rbac_user_role');
    }
    
    public function initial() : bool
    {
        $table = $this->getTable();        
        if ( ! parent::initial() &&
                $table == 'rbac_user_role') {
            $nowdate = \date('Y-m-d H:i:s');
            $sql =  "INSERT INTO $table (id,created_at,user_id,role_id) " .
                    "VALUES (1,'$nowdate',1,1)";

            if ($this->dataobject()->exec($sql) === false) {
                return false;
            }
        }
        
        return true;
    }
}
