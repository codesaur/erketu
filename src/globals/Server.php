<?php declare(strict_types=1);

namespace codesaur\Globals;

class Server extends superGlobal
{
    public function has($var_name): bool
    {
        return isset($_SERVER[$var_name]);
    }

    public function direct(): array
    {
        return $_SERVER;
    }

    public function raw($var_name)
    {
        return $_SERVER[$var_name];
    }
    
    public function checkIP(string $ip): bool
    {
        $real = ip2long($ip);
        if (empty($ip) || $real === -1 || $real === false) {
            return false;
        }
        
        $private_ips = array(
            ['0.0.0.0', '2.255.255.255'],
            ['10.0.0.0', '10.255.255.255'],
            ['127.0.0.0', '127.255.255.255'],
            ['169.254.0.0', '169.254.255.255'],
            ['172.16.0.0', '172.31.255.255'],
            ['192.0.2.0', '192.0.2.255'],
            ['192.168.0.0', '192.168.255.255'],
            ['255.255.255.0', '255.255.255.255']);
        foreach ($private_ips as $r) {
            $min = ip2long($r[0]); $max = ip2long($r[1]);
            if ($real >= $min && $real <= $max) {
                return false;
            }
        }

        return true;
    }
    
    public function getRemoteAddr(): string
    {
        if ($this->has('HTTP_X_FORWARDED_FOR')
                && $this->has('HTTP_CLIENT_IP')
                && $this->checkIP($this->raw('HTTP_CLIENT_IP'))) {
            return $this->raw('HTTP_CLIENT_IP');
        }
        
        if ($this->has('HTTP_X_FORWARDED_FOR')) {
            foreach (explode(',', $this->raw('HTTP_X_FORWARDED_FOR')) as $ip) {
                if ($this->checkIP(trim($ip))) {
                    return $ip;
                }
            }
        }
        
        if ($this->has('HTTP_X_FORWARDED')
                && $this->checkIP($this->raw('HTTP_X_FORWARDED'))) {
            return $this->raw('HTTP_X_FORWARDED');
        } elseif ($this->has('HTTP_X_CLUSTER_CLIENT_IP')
                && $this->checkIP($this->raw('HTTP_X_CLUSTER_CLIENT_IP'))) {
            return $this->raw('HTTP_X_CLUSTER_CLIENT_IP');
        } elseif ($this->has('HTTP_FORWARDED_FOR')
                && $this->checkIP($this->raw('HTTP_FORWARDED_FOR'))) {
            return $this->raw('HTTP_FORWARDED_FOR');
        } elseif ($this->has('HTTP_FORWARDED')
                && $this->checkIP($this->raw('HTTP_FORWARDED'))) {
            return $this->raw('HTTP_FORWARDED');
        } else {
            return $this->raw('REMOTE_ADDR');
        }
    }
}
