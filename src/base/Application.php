<?php namespace codesaur\Base;

use codesaur\Http\Header;
use codesaur\Http\Router;
use codesaur\Http\Request;
use codesaur\Http\Response;
use codesaur\Globals\Session;

class Application extends Base implements ApplicationInterface
{
    private $_config;
    private $_namespace;

    public $request;
    public $router;
    public $header;
    public $response;

    public $user;
    public $session;
    public $language;
    public $translation;
    
    public $route;
    public $controller;
    
    function __construct(array $config)
    {
        if (empty($config)) {
            return $this->error('Invalid application configuration!');
        }
        
        $this->request = new Request();
        $this->request->initFromGlobal();
        
        $this->router = new Router();
        $this->header = new Header();
        
        $this->response = new Response();

        $this->user = new User();
        $this->session = new Session();
        $this->language = new Language();
        $this->translation = new Translation();
        
        $url_segments = $this->request->getUrlSegments();
        if ( ! empty($url_segments)) {
            $uri = '/' . $url_segments[0];
            
            if (isset($config[$uri])) {
                $request_url = $this->request->getCleanUrl();
                $shifted = \substr($request_url, \strlen($uri));

                $this->request->setApp($uri);
                $this->request->shiftUrlSegments();
                $this->request->forceCleanUrl($shifted);

                $this->_namespace = $config[$uri];
            }
        }

        if ( ! isset($this->_namespace)) {
            if ( ! isset($config['/'])) {
                return $this->error('Default application not found!');
            }
            
            $this->_namespace = $config['/'];
        }
        
        $this->_config = $config;
    }
    
    public function launch()
    {   
        try {
            $routing = $this->getNamespace() . 'Routing';
            if ( ! \class_exists($routing)) {
                throw new \Exception("$routing not found! [URL: " . ($this->request->getUrl() ?? '') . ']');
            }

            $this->route = (new $routing())->match($this->router, $this->request);
            if ( ! isset($this->route)) {
                throw new \Exception('Unknown route!');
            }

            $controller = $this->route->getController();
            if ( ! \class_exists($controller)) {
                throw new \Exception("$controller is not available!");
            }

            $action = $this->route->getAction();
            $this->controller = new $controller();
            if ( ! \method_exists($this->controller, $action)) {
                throw new \Exception("Action named $action is not part of $controller!");
            }

            $this->execute($this->controller, $action, $this->route->getParameters()); exit;
        } catch (\Exception $ex) {            
            $this->error($ex->getMessage());
        }
    }
    
    public function execute($class, string $action, array $args)
    {
        if (\getenv('OUTPUT_COMPRESS', true) == 'true') {
            $this->response->start(array($this->response->ob, 'compress'), 0, PHP_OUTPUT_HANDLER_STDFLAGS);
        } else {
            $this->response->start();
        }
        
        $this->callFuncArray(array($class, $action), $args);
        
        $this->response->send();
    }
    
    public function error(string $message, int $code = 404)
    {
        if ( ! \headers_sent()) {
            \http_response_code($code);
        }
        
        \error_log("Error[$code]: $message");
        
        try {
            $controller = $this->getNamespace() . 'ErrorController';
            $this->execute(new $controller(), 'error', ['error' => $message, 'status' => $code]);
        } catch (\Throwable $t) {
            $host = (( ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
            $host .= $_SERVER['HTTP_HOST'] ?? 'localhost';

            if (DEBUG) {
                echo "<pre>$t</pre>";
                $notice = "<hr><strong>Output: </strong><br/>" . \ob_get_contents();
                \ob_end_clean();
            } else {
                $notice = '';
            }

            echo    '<!doctype html><html lang="en"><head><meta charset="utf-8"/><title>Error' .
                    " $code</title></head><body><h1>Error $code</h1><p>$message</p><hr>" .
                    '<a href="' . $host . '">' . "$host</a>$notice</body></html>";
        }
    }

    public function getNamespace()
    {
        return $this->_namespace;
    }
    
    public function getConfiguraton() : ?array
    {
        return $this->_config;
    }

    public function getWebUrl(bool $relative) : string
    {
        if ($relative) {
            return $this->request->getPath();
        }
        
        return $this->request->getHttpHost() . $this->request->getPath();
    }
    
    public function getPublicUrl(bool $relative = true) : string
    {
        return $this->getWebUrl($relative) . '/public';
    }

    public function getResourceUrl(bool $relative = true) : string
    {
        return $this->getWebUrl($relative) . '/resource';
    }
}
