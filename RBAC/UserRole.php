<?php namespace codesaur\RBAC;

use codesaur\DataObject\CDO;
use codesaur\DataObject\Column;
use codesaur\DataObject\Describe;
use codesaur\MultiModel\InitableModel;

class UserRole extends InitableModel
{
    function __construct(CDO $conn)
    {
        parent::__construct($conn);
        
        $this->structure((new Describe())->create(
                array(
                   (new Column('id', 'bigint', 20))->auto()->primary()->unique()->notNull(),
                   (new Column('user_id', 'bigint', 20))->notNull()->foreignKey('accounts(id)'),
                   (new Column('role_id', 'bigint', 20))->notNull()->foreignKey('rbac_roles(id)'),
                    new Column('is_active', 'tinyint', 1, 1),
                    new Column('created_at', 'datetime'),
                   (new Column('created_by', 'bigint', 20))->foreignKey('accounts(id)'),
                    new Column('updated_at', 'datetime'),
                   (new Column('updated_by', 'bigint', 20))->foreignKey('accounts(id)')
                )
        ));
        
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
