<?php namespace codesaur\Base;

class Authentication
{
    const Unset  = 0;
    const Login  = 1;
    const Locked = 2;
}

class User
{
    private $_rbac;
    private $_account;
    private $_organizations;
    
    private $_status = Authentication::Unset;
    
    public function login(?array $account, ?array $organizations, ?array $rbac)
    {
        if ( ! isset($account['id'])
                || ! isset($organizations[0]['id'])) {           
            throw new \Exception('Invalid user information!');
        }

        $this->_rbac = $rbac;
        $this->_account = $account;
        $this->_organizations = $organizations;

        $this->_status = Authentication::Login;
        
        \putenv(_ACCOUNT_ID_ . "={$account['id']}");
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
    
    public function organizations(): ?array
    {
        return $this->_organizations;
    }

    public function is($role): bool
    {        
        if (isset($this->_rbac['system_coder'])) {
            return true;
        }
        
        return isset($this->_rbac[$role]);
    }

    public function can($permission, $role = null): bool
    {
        if (isset($this->_rbac['system_coder'])) {
            return true;
        }
        
        if ( ! empty($role)) {
            return $this->_rbac[$role][$permission] ?? false;
        }
        
        foreach ($this->_rbac as $role) {
            if (isset($role[$permission])) {
                return $role[$permission] == true;
            }
        }
        
        return false;
    }
}
