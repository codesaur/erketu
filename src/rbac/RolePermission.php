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

/*
 * TODO: make role permissions initial!
system_account_delete
system_account_index
system_account_initial
system_account_insert
system_account_organization_set
system_account_retrieve
system_account_update
system_developer_index
system_documentation_index
system_file_index
system_forgot_index
system_image_index
system_indoraptor_index
system_language_delete
system_language_index
system_language_initial
system_language_insert
system_language_retrieve
system_language_update
system_log_index
system_mailer
system_newbie_index
system_org_delete
system_org_index
system_org_initial
system_org_insert
system_org_retrieve
system_org_update
system_pages_delete
system_pages_index
system_pages_retrieve
system_pages_update
system_rbac
system_reference_index
system_system_mailer
system_template_delete
system_template_index
system_template_initial
system_template_insert
system_template_retrieve
system_template_update
system_translation_delete
system_translation_index
system_translation_initial
system_translation_insert
system_translation_retrieve
system_translation_update
 */