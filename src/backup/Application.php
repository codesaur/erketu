<?php declare(strict_types=1);

namespace codesaur\Backup;

use codesaur\Backup\Response;
use codesaur\Backup\ServerRequest;
use codesaur\Container\Container;

class Application
{
    private $_container;
    private $_router;
    private $_middlewares;
    
    function __construct()
    {
        $this->_container = new Container();
        
        $this->_router = new Router();        
        $this->_middlewares = array();
    }

    public function &router(): Router
    {
        return $this->_router;
    }

    public function __call(string $name, array $arguments)
    {
        if (count($arguments) == 0) {
            // TODO: throw empty arguments error
            return;
        }
                
        switch ($name)
        {
            case 'map':
            case 'any':
            case 'get':
            case 'post':
            case 'put':
            case 'patch':
            case 'delete':
                return call_user_func_array(array($this->_router, $name), $arguments);
                
            case 'use':
                if ($arguments[0] instanceof Router) {
                    return $this->_router->merge($arguments[0]);
                }
                break;
        }
    }
    
    public function handle(ServerRequest $request, \Closure $next = null) : Response
    {
        $response = new Response();
        return $this->execute($request, $response);
    }

    public function execute(ServerRequest &$request, Response &$response)
    {
        try {
            $route = $this->router()->match($request->getCleanUrl(), $request->getMethod());
            if (!isset($route)) {
                throw new \Exception('Unknown route!');
            }
            
            $request->setParams($route->getParameters());
            
            $callback = $route->getCallback();
            
            if ($callback instanceof \Closure) {
                call_user_func_array($callback, array($request, $response));
            } else {
                $controllerClass = $callback[0];
                if (!class_exists($controllerClass)) {
                    throw new \Exception("$controllerClass is not available!");
                }

                $action = $callback[1] ?? 'index';
                $controller = new $controllerClass($request, $response);
                if (!method_exists($controller, $action)) {
                    throw new \Exception("Action named $action is not part of $controllerClass!");
                }
                
                call_user_func_array(array($controller, $action), $route->getParameters());             
            }
        } catch (\Exception $ex) {
            $response->error($ex->getMessage(), 404);
        } finally {
            return $response;
        }
    }
}
