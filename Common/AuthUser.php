<?php namespace codesaur\Common;

class Authentication
{
    const Unset = 0;
    const Login = 1;
    const Locked = 2;
}

class AuthUser extends Base
{
    protected $auth;
    protected $data;
    
    function __construct()
    {
        $this->auth = Authentication::Unset;
    }
    
    public function login($data)
    {
        $this->data = $data;
        
        $this->auth = Authentication::Login;
        
        if (isset($data['account']['id'])
                && \is_int($data['account']['id'])) {
            \putenv(_ACCOUNT_ID_ . "={$data['account']['id']}");
        }
    }

    public function logout()
    {
        $this->auth = Authentication::Unset;
    }

    public function lock()
    {
        $this->auth = Authentication::Locked;
    }

    public function isLogin(): bool
    {
        return $this->auth == Authentication::Login;
    }

    public function isLocked(): bool
    {
        return $this->auth == Authentication::Locked;
    }

    public function notLogin(): bool
    {
        return ! $this->isLogin();
    }
    
    public function data() : array
    {
        return $this->data;// ?? array('rbac' => array(), 'account' => array(), 'organization' => array());
    }
    
    public function has($data, $index) : bool
    {
        return isset($this->data[$data][$index]);
    }
    
    public function set($index, $value)
    {
        $this->data[$index] = $value;
    }
    
    public function &account($index = null)
    {
        if ( ! isset($index)) {
            return $this->data['account'];
        }
        
        if ($this->has('account', $index)) {
            return $this->data['account'][$index];
        }
        
        $nulldata = null;
        return $nulldata;
    }
    
    public function &organization($index = null)
    {
        if ( ! isset($index)) {
            return $this->data['organization'];
        }
        
        if ($this->has('organization', $index)) {
            return $this->data['organization'][$index];
        }
        
        $nulldata = null;
        return $nulldata;
    }

    public function hasRole() : bool
    {
        if ( ! $this->data['rbac'] instanceof Base ||
                ! $this->data['rbac']->hasMethod('hasRole')) {
            return false;
        }
        
        return true;
    }
    
    public function is($role) : bool
    {
        if ( ! $this->hasRole()) {
            return false;
        }
        
        if ($this->data['rbac']->hasRole('system_coder')) {
            return true;
        }
        
        return $this->data['rbac']->hasRole($role);
    }

    public function can($permission, $role = null) : bool
    {
        if ( ! $this->hasRole()) {
            return false;
        }
        
        if ($this->data['rbac']->hasRole('system_coder')) {
            return true;
        }
        
        return $this->data['rbac']->hasPrivilege($permission, $role);
    }
    
    public function getAlias()
    {
        return $this->data['organization']['alias'] ?? null;
    }
}
