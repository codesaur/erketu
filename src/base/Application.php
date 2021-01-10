<?php namespace codesaur\Base;

use codesaur\Http\Router;
use codesaur\Http\Request;
use codesaur\Http\Response;

class Application extends Base implements ApplicationInterface
{
    private $_router;
    private $_request;
    private $_response;
    
    function __construct(Request $request, Response $response)
    {
        $this->_request = $request;
        $this->_response = $response;
        
        $this->_router = new Router();
    }

    public function &router() : Router
    {
        return $this->_router;
    }
    
    public function &request() : Request
    {
        return $this->_request;
    }
    
    public function &response() : Response
    {
        return $this->_response;
    }
    
    public function handle()
    {
        try {
            if (\getenv('OUTPUT_COMPRESS', true) == 'true') {
                $this->response()->getBuffer()->startCompress();
            } else {
                $this->response()->getBuffer()->start();
            }

            $route = $this->router()->match($this->request()->getCleanUrl(), $this->request()->getMethod());
            if ( ! isset($route)) {
                throw new \Exception('Unknown route!');
            }
            
            $this->request()->setParams($route->getParameters());
            
            if ($route->isCallable()) {
                $this->callFuncArray($route->getCallback(), array($this->request(), $this->response()));
            } else {
                $controllerClass = $route->getController();
                if ( ! \class_exists($controllerClass)) {
                    throw new \Exception("$controllerClass is not available!");
                }

                $action = $route->getAction();
                $controller = new $controllerClass($this);
                if ( ! \method_exists($controller, $action)) {
                    throw new \Exception("Action named $action is not part of $controllerClass!");
                }
                
                $this->callFuncArray(array($controller, $action), $route->getParameters()); 
            }       
        } catch (\Throwable $t) {
            $this->response()->error($t->getMessage(), 404, $t);
        } finally {
            $this->response()->getBuffer()->endFlush();
        }
    }
    
    public function map(string $path, $target, array $args = array())
    {
        try {
            $this->_router->map($path, $target, $args);
        } catch (\Exception $ex) {
            $this->response()->error($ex->getMessage());
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
}
