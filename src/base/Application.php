<?php namespace codesaur\Base;

use codesaur\Http\Router;
use codesaur\Http\Request;
use codesaur\Http\Response;
use codesaur\Http\Controller;

class Application extends Base implements ApplicationInterface
{
    private $_router;
    private $_base_url;
    private $_base_path;
    private $_controller;
    
    function __construct()
    {
        $this->_router = new Router();
    }
    
    public function handle(Request &$request, Response &$response)
    {
        try {
            if (\getenv('OUTPUT_COMPRESS', true) == 'true') {
                $response->getBuffer()->startCompress();
            } else {
                $response->getBuffer()->start();
            }
            
            $this->_base_path = $request->getPath();
            $this->_base_url = $request->getHttpHost() . $this->_base_path;

            $route = $this->getRouter()->match($request->getCleanUrl(), $request->getMethod());
            if ( ! isset($route)) {
                throw new \Exception('Unknown route!');
            }
            
            if ($route->isCallable()) {
                $callback = $route->getCallback();
            } else {
                $controller = $route->getController();
                if ( ! \class_exists($controller)) {
                    throw new \Exception("$controller is not available!");
                }

                $action = $route->getAction();
                $this->_controller = new $controller();
                if ( ! \method_exists($this->getController(), $action)) {
                    throw new \Exception("Action named $action is not part of $controller!");
                }
                
                $callback = array($this->getController(), $action);
            }

            $this->callFuncArray($callback, $route->getParameters());        
        } catch (\Throwable $t) {
            $this->error($t->getMessage(), 404, $t);
        } finally {
            $response->getBuffer()->endFlush();
        }
    }
    
    public function error(string $message, int $status = 404, \Throwable $t = null)
    {
        if ( ! \headers_sent()) {
            \http_response_code($status);
        }
        
        \error_log("Error[$status]: $message");
        
        $host = (( ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        $host .= $_SERVER['HTTP_HOST'] ?? 'localhost';

        if (DEBUG && ! empty($t)) {
            \ob_start(null, 0, PHP_OUTPUT_HANDLER_STDFLAGS);

            echo "<pre>$t</pre>";

            $notice = '<hr><strong>Output: </strong><br/>';
            $notice .= \ob_get_contents();

            \ob_end_clean();
        }

        echo    '<!doctype html><html lang="en"><head><meta charset="utf-8"/>' .
                "<title>Error $status</title></head><body><h1>Error $status</h1>" .
                "<p>$message</p><hr><a href=\"$host\">$host</a>" . ($notice ?? '') .
                '</body></html>';
    }
    
    public function route(string $path, $target, array $args = array())
    {
        try {
            $this->_router->map($path, $target, $args);
        } catch (\Exception $ex) {
            die($this->error($ex->getMessage()));
        }
    }
    
    public function any(string $path, callable $callback, ?string $name = null)
    {
        $this->_router->mapCallback($path, $callback, $name, array('GET', 'POST', 'PUT', 'PATCH', 'DELETE'));
    }
    
    public function get(string $path, callable $callback, ?string $name = null)
    {
        $this->_router->mapCallback($path, $callback, $name, array('GET'));
    }
    
    public function post(string $path, callable $callback, ?string $name = null)
    {
        $this->_router->mapCallback($path, $callback, $name, array('POST'));
    }
    
    public function put(string $path, callable $callback, ?string $name = null)
    {
        $this->_router->mapCallback($path, $callback, $name, array('PUT'));
    }
    
    public function patch(string $path, callable $callback, ?string $name = null)
    {
        $this->_router->mapCallback($path, $callback, $name, array('PATCH'));
    }
    
    public function delete(string $path, callable $callback, ?string $name = null)
    {
        $this->_router->mapCallback($path, $callback, $name, array('DELETE'));
    }

    public function &getRouter() : Router
    {
        return $this->_router;
    }

    public function getController() : ?Controller
    {
        return $this->_controller ?? null;
    }

    public function getBaseUrl(bool $absolute = true) : string
    {
        return $absolute ? $this->_base_url : $this->_base_path;
    }
}
