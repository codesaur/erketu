<?php namespace codesaur\Globals;

class Session extends superGlobal
{
    private $_id;
            
    public function has(string $var_name): bool
    {
        return parent::has_var(INPUT_SESSION, $var_name);
    }

    public function value(string $var_name, int $filter = FILTER_DEFAULT, $options = null)
    {
        return parent::filter(INPUT_SESSION, $var_name, $filter, $options);
    }

    public function direct(): array
    {
        return $_SESSION;
    }

    public function raw($var_name)
    {
        return $_SESSION[$var_name];
    }
    
    public function start($name, $options = array(), $lifetime = null, $path = '/', $domain = null, bool $secure = true, bool $httponly = true)
    {
        \session_name($name);
        
        $this->_id = \session_id();
        if ( ! empty($this->_id)) {
            return;
        }
        
        if ($lifetime === null) {
            $lifetime = \time() + 30 * 24 * 60 * 60;
        }
        
        \session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
        \session_start($options);
        
        $this->_id = \session_id();
    }
    
    public function getID()
    {
        return $this->_id;
    }   

    public function check(string $varname): bool
    {
        return isset($_SESSION[$varname]);
    }
    
    public function get(string $name)
    {
        return $this->raw($name);
    }
    
    public function set(string $name, $value)
    {
        $_SESSION[$name] = $value;
    }
    
    public function release(string $name)
    {
        unset($_SESSION[$name]);
    }
    
    public function lock()
    {
        \session_write_close();
    }

    public function destroy()
    {
        \session_destroy();
    }
}
