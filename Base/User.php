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
    private $_organizations;
    private $_role_permissions;
    
    private $_status = Authentication::Unset;
    
    public function login(array $account, array $organizations, array $role_permissions)
    {
        if (isset($this->_account['id'])
                && \is_int($this->_account['id'])) {
            $this->_status = Authentication::Login;
            \putenv(_ACCOUNT_ID_ . "={$this->_account['id']}");
        } else {
            return;
        }

        $this->_account = $account;
        $this->_organizations = $organizations;
        $this->_role_permissions = $role_permissions;
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
    
    public function account($key)
    {
        return $this->_account[$key] ?? null;
    }
    
    public function organization($key)
    {
        return $this->_organizations[0][$key] ?? null;
    }
    
    public function organizations() : ?array
    {
        return $this->_organizations;
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
