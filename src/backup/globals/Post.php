<?php namespace codesaur\Backup\Globals;

class Post extends superGlobal
{
    public function has(string $var_name): bool
    {
        return parent::has_var(INPUT_POST, $var_name);
    }
    
    public function hasArray(array $names): bool
    {
        foreach ($names as $name) {
            if ( ! $this->has($name)) {
                return false;
            }
        }
        
        return true;
    }
    
    public function value(string $var_name, int $filter = FILTER_DEFAULT, $options = null)
    {
        return parent::filter(INPUT_POST, $var_name, $filter, $options);
    }

    public function direct(): array
    {
        return $_POST;
    }

    public function raw($var_name)
    {
        return $_POST[$var_name];
    }
    
    public function asString($var): string
    {
        if (isset($var)) {
            return \filter_var($var, FILTER_SANITIZE_STRING);
        }
        
        return '';
    }

    public function asInt($var): int
    {
        if (isset($var)) {
            return \filter_var($var, FILTER_VALIDATE_INT);
        }
        
        return 0;
    }

    public function asFiles($var)
    {
        if (isset($var)) {
            return \filter_var($var, FILTER_DEFAULT);
        }
        
        return '';
    }

    public function asEmail($var)
    {
        if (isset($var)) {
            return \filter_var($var, FILTER_VALIDATE_EMAIL);
        }
        
        return '';
    }
    
    final public function asPassword($var, $verify = false)
    {
        if (defined('CRYPT_BLOWFISH') && CRYPT_BLOWFISH) {
            if ($verify) {
                return \password_verify(\filter_var($var, FILTER_SANITIZE_STRING), $verify);
            } else {
                return \password_hash(\filter_var($var, FILTER_SANITIZE_STRING), PASSWORD_BCRYPT);
            }
        } else {
            if ($verify) {
                return \md5(\filter_var($var, FILTER_SANITIZE_STRING)) === $verify;
            } else {
                return \md5(\filter_var($var, FILTER_SANITIZE_STRING));
            }
        }
    }
}
