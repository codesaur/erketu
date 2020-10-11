<?php namespace codesaur\Common;

use codesaur\Http\Header;
use codesaur\Http\Router;
use codesaur\Http\Request;
use codesaur\Http\Response;

use codesaur\Globals\Session;

class Application extends Base implements ApplicationInterface
{
    private $_namespace;

    public $request;
    public $router;
    public $header;
    public $session;
    public $response;

    public $route;
    public $controller;    
    
    public $user;
    
    public $language;
    public $translation;
    
    function __construct(array $config)
    {
        if (empty($config)) {
            return $this->error('Invalid application configuration!');
        }
        
        $this->initComponents();
        
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
            if (isset($config['/'])) {
                $this->_namespace = $config['/'];
            } else {
                return $this->error('Default application not found!');
            }
        }
    }
    
    private function initComponents()
    {
        $this->request = new Request();
        $this->request->initFromGlobal();
        
        $this->router = new Router();
        $this->header = new Header();

        $this->session = new Session();
        $this->session->start('codesaur');
        
        $this->response = new Response();

        $this->user = new AuthUser();
        
        $this->translation = new Translation();
        $this->language = new Language($this->session);
    }
    
    public function getNamespace()
    {
        return $this->_namespace;
    }

    public function launch()
    {   
        $routing = $this->getNamespace() . 'Routing';

        if ( ! \class_exists($routing)) {
            return $this->error("$routing not found! [URL: " . ($this->request->getUrl() ?? '') . ']');
        }

        $this->route = (new $routing())->match($this->router, $this->request);
        
        if ( ! isset($this->route)) {
            return $this->error('Unknown route!');
        }

        $controller = $this->route->getController();

        if ( ! \class_exists($controller)) {
            return $this->error("{$this->route->getController()} is not available!");
        }
        
        $this->controller = new $controller();
        if (! $this->controller->hasMethod($this->route->getAction())) {
            return $this->error("Action named {$this->route->getAction()} is not part of {$this->route->getController()}!");
        }

        $this->execute($this->controller, $this->route->getAction(), $this->route->getParameters()); exit;
    }
    
    public function execute($class, string $action, array $args)
    {
        if (\getenv('OUTPUT_COMPRESS') == 'true') {
            $this->response->start(array($this->response->ob, 'compress'), 0, PHP_OUTPUT_HANDLER_STDFLAGS);
        } else {
            $this->response->start();
        }
        
        $this->callFuncArray(array($class, $action), $args);
        
        $this->response->send();
    }
    
    public function error(string $message, int $status = 404)
    {
        if ( ! \headers_sent()) {
            \http_response_code($status);
        }
        
        \error_log("Error[$status]: $message");
        
        try {
            $controller = $this->getNamespace() . 'Error\\ErrorController';
            $this->execute(new $controller(), 'error', ['error' => $message, 'status' => $status]);
        } catch (\Throwable $t) {
            $host = 'https://';
            $host .= $_SERVER['HTTP_HOST'] ?? 'localhost';

            if (DEBUG) {
                echo "<pre>$t</pre>";
                $notice = "<hr><strong>Output: </strong><br/>" . \ob_get_contents();
                \ob_end_clean();
            }

            echo    '<!doctype html><html lang="en"><head><meta charset="utf-8"/><title>' . 'Error ' .
                    $status . '</title></head><body><h1>Error ' . $status . '</h1><p>' . $message .
                    '</p><hr><a href="' . $host . '">' . $host . '</a>' . ($notice ?? null) . '</body></html>';
        }
        
        exit;
    }

    public function webUrl(bool $relative) : string
    {
        if ($relative) {
            return $this->request->getPath();
        }
        
        return $this->request->getHttpHost() . $this->request->getPath();
    }

    public function publicUrl(bool $relative = true) : string
    {
        return $this->webUrl($relative) . '/public';
    }

    public function resourceUrl(bool $relative = true) : string
    {
        return $this->webUrl($relative) . '/resource';
    }
}
