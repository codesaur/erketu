<?php namespace codesaur\Base;

class Authentication
{
    const Unset = 0;
    const Login = 1;
    const Locked = 2;
}

class User extends Base
{
    private $_account;
    private $_organization;
    private $_role_permissions;
    
    private $_status = Authentication::Unset;
    
    public function login(array $account, array $org, array $roles)
    {
        $this->_account = $account;
        $this->_organization = $org;
        $this->_role_permissions = $roles;       
        
        $this->_status = Authentication::Login;
        
        if (isset($this->_account['id'])
                && \is_int($this->_account['id'])) {
            \putenv(_ACCOUNT_ID_ . "={$this->_account['id']}");
        }
    }

    public function logout()
    {
        $this->_status = Authentication::Unset;
    }

    public function lock()
    {
        $this->_status = Authentication::Locked;
    }

    public function isLogin(): bool
    {
        return $this->_status == Authentication::Login;
    }

    public function isLocked(): bool
    {
        return $this->_status == Authentication::Locked;
    }
    
    public function account($index)
    {
        return $this->_account[$index] ?? null;
    }
    
    public function organization($index)
    {
        return $this->_organization[$index] ?? null;
    }
    
    public function is($role) : bool
    {        
        if (isset($this->_role_permissions['system_coder'])) {
            return true;
        }
        
        return isset($this->_role_permissions[$role]);
    }

    public function can($permission, $role = null) : bool
    {
        if (isset($this->_role_permissions['system_coder'])) {
            return true;
        }
        
        if ( ! empty($role)) {
            return ($this->_role_permissions[$role][$permission] ?? false) == true;
        }
        
        foreach ($this->_role_permissions as $role) {
            if (isset($role[$permission])) {
                return $role[$permission] == true;
            }
        }
        
        return false;
    }
}
