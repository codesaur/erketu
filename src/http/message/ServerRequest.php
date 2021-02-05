<?php declare(strict_types=1);

namespace codesaur\Http\Message;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

use codesaur\Globals\Server;
use codesaur\Globals\Cookie;

class ServerRequest extends Request implements ServerRequestInterface
{
    protected $serverParams = array();
    protected $cookies = array();
    protected $attributes = array();

    protected $queryParams;
    protected $parsedBody;
    protected $uploadedFiles;

    public function initFromGlobal()
    {
        $server = new Server();        
        $this->serverParams = $server->direct();
        if (isset($this->serverParams['SERVER_PROTOCOL'])) {
            $this->protocolVersion = str_replace('HTTP/', '', $this->serverParams['SERVER_PROTOCOL']);
        }
        
        $this->method = strtoupper($this->serverParams['REQUEST_METHOD']);
        
        $cookie = new Cookie();
        $this->cookies = $cookie->direct();
        
        $this->uri = new Uri();        
        $https = $this->serverParams['HTTPS'] ?? 'off';
        $port = (int)$this->serverParams['SERVER_PORT'];
        if ((!empty($https) && strtolower($https) !== 'off')
                || $port === 443
        ) {
            $this->uri->setScheme('https');
        } else {
            $this->uri->setScheme('http');
        }
        $this->uri->setPort($port);
        
        $this->uri->setHost($this->serverParams['HTTP_HOST']);
        $this->setHeader('Host', $this->uri->getHost());

        $path = preg_replace('/\/+/', '\\1/', $this->serverParams['REQUEST_URI']); 
        if (($pos = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $pos);
        }        

        $base_name = basename($this->serverParams['SCRIPT_NAME']);
        $base_name_length = strlen($base_name);
        if (substr($path, 1, $base_name_length)
                === $base_name
        ) {
            $path = substr($path, $base_name_length + 1);
        }        
        $this->requestTarget = rtrim($path, '/');
        $this->uri->setPath($this->requestTarget);
        
        if (!empty($this->serverParams['QUERY_STRING'])) {
            $this->uri->setQuery($this->serverParams['QUERY_STRING']);
            $this->requestTarget .= "?{$this->serverParams['QUERY_STRING']}";
        }
        
        $input = file_get_contents('php://input');
        if (!empty($input)) {
            $this->parsedBody = json_decode($input, true);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieParams(): array
    {
        return $this->cookies;
    }

    /**
     * {@inheritdoc}
     */
    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->cookies = $cookies;

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParams() : array
    {
        if (is_array($this->queryParams)) {
            return $this->queryParams;
        }

        if (!$this->getUri() instanceof UriInterface) {
            return array();
        }
        
        $query = rawurldecode($this->getUri()->getQuery());
        parse_str($query, $this->queryParams);

        return $this->queryParams;
    }

    /**
     * {@inheritdoc}
     */
    public function withQueryParams(array $query)
    {
        $clone = clone $this;
        $clone->queryParams = $query;

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles ?? array();
    }

    /**
     * {@inheritdoc}
     */
    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;
        
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * {@inheritdoc}
     */
    public function withParsedBody($data): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->parsedBody = $data;

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes(): array
    {
        return $this->attributes ?? array();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name, $default = null)
    {
        if (!isset($this->attributes[$name])) {
            return $default;
        }
        
        return $this->attributes[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function withAttribute($name, $value): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;
        
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutAttribute($name): ServerRequestInterface
    {
        $clone = clone $this;
        unset($clone->attributes[$name]);
        
        return $clone;
    }
}
