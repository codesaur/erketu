<?php namespace codesaur\RBAC;

use codesaur\DataObject\CDO;
use codesaur\DataObject\InitableModel;

class Permissions extends InitableModel
{
    function __construct(CDO $conn)
    {
        parent::__construct($conn);
        
        $this->structure(new PermissionsDescribe());
        
        $this->setTable('rbac_permissions');
    }
    
    public function initial(): bool
    {
        $table = $this->getTable();
        if ( ! parent::initial()
                && $table != 'rbac_permissions') {
            return false;
        }
        
        $nowdate = \date('Y-m-d H:i:s');
        $sql = "INSERT INTO $table (created_at,alias,module,name,description) "
                . "VALUES ('$nowdate','system','system','system_mailer',''),"
                . "('$nowdate','system','account','account_index',''),"
                . "('$nowdate','system','account','account_retrieve',''),"
                . "('$nowdate','system','account','account_insert',''),"
                . "('$nowdate','system','account','account_update',''),"
                . "('$nowdate','system','account','account_delete',''),"
                . "('$nowdate','system','account','account_initial',''),"
                . "('$nowdate','system','account','account_rbac',''),"
                . "('$nowdate','system','account','account_newbie_index',''),"
                . "('$nowdate','system','account','account_forgot_index',''),"
                . "('$nowdate','system','account','account_organization_set',''),"
                . "('$nowdate','system','organization','org_index',''),"
                . "('$nowdate','system','organization','org_retrieve',''),"
                . "('$nowdate','system','organization','org_insert',''),"
                . "('$nowdate','system','organization','org_update',''),"
                . "('$nowdate','system','organization','org_delete',''),"
                . "('$nowdate','system','organization','org_initial',''),"
                . "('$nowdate','system','developer','developer_index',''),"
                . "('$nowdate','system','documentation','documentation_index',''),"
                . "('$nowdate','system','documentation','indoraptor_index',''),"
                . "('$nowdate','system','content','template_index',''),"
                . "('$nowdate','system','content','template_retrieve',''),"
                . "('$nowdate','system','content','template_insert',''),"
                . "('$nowdate','system','content','template_update',''),"
                . "('$nowdate','system','content','template_delete',''),"
                . "('$nowdate','system','content','template_initial',''),"
                . "('$nowdate','system','content','reference_index',''),"
                . "('$nowdate','system','content','reference_initial',''),"
                . "('$nowdate','system','content','pages_index',''),"
                . "('$nowdate','system','content','pages_retrieve',''),"
                . "('$nowdate','system','content','pages_insert',''),"
                . "('$nowdate','system','content','pages_update',''),"
                . "('$nowdate','system','content','pages_delete',''),"
                . "('$nowdate','system','content','files_index',''),"
                . "('$nowdate','system','content','images_index',''),"
                . "('$nowdate','system','localization','language_index',''),"
                . "('$nowdate','system','localization','language_retrieve',''),"
                . "('$nowdate','system','localization','language_insert',''),"
                . "('$nowdate','system','localization','language_update',''),"
                . "('$nowdate','system','localization','language_delete',''),"
                . "('$nowdate','system','localization','language_initial',''),"
                . "('$nowdate','system','localization','translation_index',''),"
                . "('$nowdate','system','localization','translation_retrieve',''),"
                . "('$nowdate','system','localization','translation_insert',''),"
                . "('$nowdate','system','localization','translation_update',''),"
                . "('$nowdate','system','localization','translation_delete',''),"
                . "('$nowdate','system','localization','translation_initial',''),"
                . "('$nowdate','system','localization','logger_index','')";
        
        if ($this->dataobject()->exec($sql) === false) {
            return false;
        }
            
        return true;
    }
}
