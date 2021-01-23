<?php namespace codesaur\Backup\RBAC;

use codesaur\Backup\DataObject\Column;
use codesaur\Backup\DataObject\Describe;

class UserRoleDescribe extends Describe
{
    function __construct()
    {
        return $this->create(
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
        );
    }
}
