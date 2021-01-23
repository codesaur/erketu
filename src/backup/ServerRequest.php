<?php namespace codesaur\Backup;

use codesaur\Backup\Globals\Server;

class ServerRequest
{
    private $_domain;
    private $_secure;
    private $_method;
    private $_httphost;

    private $_url;
    private $_path;
    private $_script;

    private $_url_clean;
    private $_url_segments;
    private $_url_app_segment = '';
    private $_url_params = array();
    
    private $_body;
    public $params;
    
    function __construct()
    {
        $server = new Server();
        
        $this->_domain = $server->raw('HTTP_HOST');
        $this->_method = $server->raw('REQUEST_METHOD');
        $this->_script = \preg_replace('/\/+/', '\\1/', $server->raw('SCRIPT_NAME'));
        $this->_secure = ( ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
        
        $this->_httphost = ($this->isSecure() ? 'https://' : 'http://') . $this->getDomain();
        
        $this->_url = \preg_replace('/\/+/', '\\1/', $server->raw('REQUEST_URI'));
        $this->_url_clean = $this->cleanUrl($this->getUrl(), $server->raw('QUERY_STRING'));
        
        $url_segments = \explode('/', $this->getCleanUrl());
        \array_shift($url_segments);
        $this->_url_segments = $url_segments;
        
        $this->_path = \preg_replace('/\/+/', '\\1/', \str_replace('/' . \basename($this->getScript()), '', $this->getScript()));
        
        $this->setBody(\file_get_contents('php://input'));
    }

    public function isSecure(): bool
    {
        return $this->_secure;
    }

    public function getDomain(): string
    {
        return $this->_domain;
    }
    
    public function getHttpHost(): string
    {
        return $this->_httphost;
    }
    
    public function getUrl(): string
    {
        return $this->_url;
    }
    
    public function getCleanUrl(): string
    {
        return $this->_url_clean;
    }
    
    public function setParams(array $params)
    {
        $this->params = new \stdClass();
        
        foreach ($params as $key => $value) {
            $this->params->$key = $value;
        }
    }
        
    public function getUrlParams(): array
    {
        return $this->_url_params;
    }
    
    public function getUrlParam($key)
    {
        return $this->_url_params[$key] ?? null;
    }

    public function hasUrlParam($key): bool
    {
        return \in_array($key, \array_keys($this->_url_params));
    }
    
    public function addUrlParam($key, $value)
    {
        if ($this->hasUrlParam($key)) {
            return false;
        }
        
        $this->_url_params[$key] = $value;
        
        return true;
    }

    public function getPath(): string
    {
        return $this->_path;
    }
    
    public function getMethod(): string
    {
        return $this->_method;
    }
    
    public function getScript(): string
    {
        return $this->_script;
    }
    
    public function getUrlSegments(): array
    {
        return $this->_url_segments;
    }
    
    public function getAppSegment(): string
    {
        return $this->_url_app_segment;
    }
    
    public function setBody($body)
    {
        $this->_body = $body;
    }
    
    public function getBody()
    {
        return $this->_body;
    }

    public function getBodyJson(bool $assoc = false, int $depth = 512, int $options = 0)
    {
        return \json_decode($this->_body, $assoc, $depth, $options);
    }

    public function getPathComplete(): string
    {
        return $this->getPath() . $this->_url_app_segment;
    }
    
    public function getQueryString(): string
    {
        if (empty($this->getUrlParams())) {
            return '';
        } else {
            return \http_build_query($this->getUrlParams());
        }
    }

    public function shiftAppSegment(string $path)
    {
        $this->_url_app_segment = \rtrim($path, '/');        
        $this->_url_clean = \substr($this->_url_clean, \strlen($path));
        
        \array_shift($this->_url_segments);
    }
    
    function recursionUrlParams(string $key, $value)
    {
        if (empty($value)) {
            return;
        }
        
        if (\is_array($value)) {
            foreach ($value as $subkey => $subvalue) {
                $this->recursionUrlParams($key . "[$subkey]", $subvalue);
            }
        } else {
            $this->_url_params[\urldecode($key)] = \urldecode($value);
        }
    }

    function cleanUrl(string $url, string $query_string): string
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
                $this->recursionUrlParams($key, $value);
            }
        }
      
        if (\substr($url, 1, \strlen(\basename($this->getScript())))
                == \basename($this->getScript())) {
            $url = \substr($url, \strlen(\basename($this->getScript())) + 1);
        }
        
        return \rtrim($url, '/');
    }
}
