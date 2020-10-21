<?php namespace codesaur\Http;

use codesaur\Base\Base;
use codesaur\Globals\Server;

class Request extends Base
{
    private $_domain;
    private $_secure;
    private $_method;
    private $_httphost;
    private $_app = '';

    private $_url;
    private $_path;
    private $_script;

    private $_url_clean;
    private $_url_segments;
    private $_url_params = array();
    
    public function initFromGlobal()
    {
        $server = new Server();
        
        $this->_secure = true;
        $this->_domain = $server->raw('HTTP_HOST');
        $this->_script = \preg_replace('/\/+/', '\\1/', $server->raw('SCRIPT_NAME'));
        $this->_method = $server->raw('REQUEST_METHOD');
        $this->_httphost = 'https://' . $this->getDomain();
        
        $this->_url = \preg_replace('/\/+/', '\\1/', $server->raw('REQUEST_URI'));
        $this->_url_clean = $this->cleanUrl($this->getUrl(), $server->raw('QUERY_STRING'));
        $this->_url_segments = \explode('/', $this->getCleanUrl());        
        $this->shiftUrlSegments();
        
        $this->_path = \preg_replace('/\/+/', '\\1/', \str_replace('/' . \basename($this->getScript()), '', $this->getScript()));
    }

    public function isSecure() : bool
    {
        return $this->_secure;
    }

    public function getDomain() : string
    {
        return $this->_domain;
    }
    
    public function getHttpHost() : string
    {
        return $this->_httphost;
    }
    
    public function getUrl() : string
    {
        return $this->_url;
    }
    
    public function getCleanUrl() : string
    {
        return $this->_url_clean;
    }
        
    public function getParams() : array
    {
        return $this->_url_params;
    }
    
    public function getParamsAsStr() : string
    {
        $params = '';
        if ( ! empty($this->getParams())) {
            foreach ($this->getParams() as $key => $value) {
                if ($params == '') {
                    $params .= '?';
                } else {
                    $params .= '&';
                }
                $params .= \urlencode($key) . '=' . \urlencode($value);
            }
        }
        
        return $params;
    }

    public function getParam($key)
    {
        return $this->_url_params[$key] ?? null;
    }

    public function hasParam($key) : bool
    {
        return \in_array($key, \array_keys($this->_url_params));
    }
    
    public function addParam($key, $value)
    {
        if ($this->hasParam($key)) {
            return false;
        }
        
        $this->_url_params[$key] = $value;
        
        return true;
    }

    public function getPath() : string
    {
        return $this->_path;
    }
    
    public function getMethod() : string
    {
        return $this->_method;
    }
    
    public function getScript() : string
    {
        return $this->_script;
    }
    
    public function getUrlSegments() : array
    {
        return $this->_url_segments;
    }

    public function getPathComplete() : string
    {
        return $this->_path . $this->_app;
    }
    
    public function setApp(string $alias)
    {
        $this->_app = \rtrim($alias, '/');
    }

    public function cleanUrl(string $url, string $query_string) : string
    {
        $dir_name = \dirname($this->getScript());
        
        if ($dir_name != '/') {
            $url = \str_replace($dir_name, '', $url);
        }
        
        if (($pos = \strpos($url, '?')) !== false) {
            $url = \substr($url, 0, $pos);
            $params = [];
            \parse_str($query_string, $params);
            foreach ($params as $key => $value) {
                $this->recursionParams($key, $value);
            }
        }
      
        if (\substr($url, 1, \strlen(\basename($this->getScript())))
                == \basename($this->getScript())) {
            $url = \substr($url, \strlen(\basename($this->getScript())) + 1);
        }
        
        return \rtrim($url, '/');
    }

    public function forceCleanUrl(string $url)
    {
        $this->_url_clean = $url;
    }

    public function recursionParams(string $key, $value)
    {
        if (empty($value)) {
            return;
        }
        
        if (\is_array($value)) {
            foreach ($value as $subkey => $subvalue) {
                $this->recursionParams($key . "[$subkey]", $subvalue);
            }
        } else {
            $this->_url_params[\urldecode($key)] = \urldecode($value);
        }
    }
    
    public function shiftUrlSegments()
    {
        \array_shift($this->_url_segments);
    }
}
