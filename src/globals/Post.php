<?php declare(strict_types=1);

namespace codesaur\Globals;

class Post extends superGlobal
{
    public function has(string $var_name): bool
    {
        return parent::has_var(INPUT_POST, $var_name);
    }
    
    public function hasVars(array $var_names): bool
    {
        foreach ($var_names as $name) {
            if (!$this->has($name)) {
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
        return parent::filter_var($var, FILTER_SANITIZE_STRING);
    }

    public function asInt($var): int
    {
        return parent::filter_var($var, FILTER_VALIDATE_INT);
    }

    public function asFiles($var)
    {
        return parent::filter_var($var);
    }

    public function asEmail($var)
    {
        return parent::filter_var($var, FILTER_VALIDATE_EMAIL);
    }
    
    final public function asPassword($var, bool $verify = false)
    {
        $value = $this->asString($var);
        
        if (!defined('CRYPT_BLOWFISH')
                || !CRYPT_BLOWFISH
        ) {
            return $verify ? md5($value) === $verify : md5($value);
        }
        
        if ($verify) {
            return password_verify($value, $verify);
        }
        
        return password_hash($value, PASSWORD_BCRYPT);
    }
}
