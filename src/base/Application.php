<?php namespace codesaur\Base;

use codesaur\Http\Router;
use codesaur\Http\Request;
use codesaur\Http\Response;

class Application extends Base
{
    private $_router;
    
    function __construct()
    {
        $this->_router = new Router();
    }

    public function &router() : Router
    {
        return $this->_router;
    }
    
    public function map(string $path, string $target, array $args = array())
    {
        $this->router()->map($path, $target, $args);
    }
    
    public function merge(Router $router)
    {
        $this->router()->merge($router);
    }
    
    public function any(string $path, callable $callback, ?string $name = null)
    {
        $this->router()->any($path, $callback, $name);
    }
    
    public function get(string $path, callable $callback, ?string $name = null)
    {
        $this->router()->get($path, $callback, $name);
    }
    
    public function post(string $path, callable $callback, ?string $name = null)
    {
        $this->router()->post($path, $callback, $name);
    }
    
    public function put(string $path, callable $callback, ?string $name = null)
    {
        $this->router()->put($path, $callback, $name);
    }
    
    public function patch(string $path, callable $callback, ?string $name = null)
    {
        $this->router()->patch($path, $callback, $name);
    }
    
    public function delete(string $path, callable $callback, ?string $name = null)
    {
        $this->router()->delete($path, $callback, $name);
    }
    
    public function handle(Request $request, Response $response)
    {
        try {
            $route = $this->router()->match($request->getCleanUrl(), $request->getMethod());
            if ( ! isset($route)) {
                throw new \Exception('Unknown route!');
            }
            
            $request->setParams($route->getParameters());
            
            if ($route->isCallable()) {
                $this->callFuncArray($route->getCallback(), array($request, $response));
            } else {
                $controllerClass = $route->getController();
                if ( ! \class_exists($controllerClass)) {
                    throw new \Exception("$controllerClass is not available!");
                }

                $action = $route->getAction();
                $controller = new $controllerClass($request, $response);
                if ( ! \method_exists($controller, $action)) {
                    throw new \Exception("Action named $action is not part of $controllerClass!");
                }
                
                $this->callFuncArray(array($controller, $action), $route->getParameters()); 
            }       
        } catch (\Exception $ex) {
            $response->error($ex->getMessage(), 404);
        }
    }
}
