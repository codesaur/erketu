<?php namespace codesaur\RBAC;

use codesaur\DataObject\Column;
use codesaur\DataObject\Describe;

class RolesDescribe extends Describe
{
    function __construct()
    {
        return $this->create(
                array(
                   (new Column('id', 'bigint', 20))->auto()->primary()->unique()->notNull(),
                   (new Column('name', 'varchar', 128))->notNull(),
                    new Column('description', 'varchar', 255),
                   (new Column('alias', 'varchar', 16))->notNull(),
                    new Column('is_active', 'tinyint', 1, 1),
                    new Column('created_at', 'datetime'),
                   (new Column('created_by', 'bigint', 20))->foreignKey('accounts(id)'),
                    new Column('updated_at', 'datetime'),
                   (new Column('updated_by', 'bigint', 20))->foreignKey('accounts(id)')
                )
        );
    }
}
