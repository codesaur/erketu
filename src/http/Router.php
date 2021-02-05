<?php declare(strict_types=1);

namespace codesaur\Http;

use Exception;
use ArgumentCountError;
use InvalidArgumentException;
use BadMethodCallException;
use BadFunctionCallException;
use OutOfRangeException;

class Router
{
    private $_routes = array();
    
    public function __call(string $method, array $properties)
    {
        if (!$this->isRoutingFunction($method)) {
            throw new BadFunctionCallException("Wrong function [$method] call for " . __CLASS__ . '!');
        }
            
        if (is_array($properties[0])) {
            $methods = $properties[0];
            array_shift($properties);
        } elseif ($method === 'any') {
            $methods = $this->getRequestMethods();
        } elseif ($method !== 'map') {
            $methods = array(strtoupper($method));
        } else {
            throw new ArgumentCountError('Bad definition of route!');
        }
        
        if (empty($properties)
                || empty($properties[1])
        ) {
            throw new BadMethodCallException('Bad method call for ' . __CLASS__ . ":$method. Invalid arguments!");
        }
        
        $route = new Route();
        $route->setPattern($properties[0]);
        if (is_array($properties[1])
                || is_callable($properties[1])
        ) {
            $route->setCallback($properties[1]);
        } elseif (is_string($properties[1])) {
            $route->setCallback(array($properties[1], 'index'));
        } else {
            throw new InvalidArgumentException("Invalid callback on route pattern [{$properties[0]}]!");
        }
        
        if (isset($properties[2])) {
            if (is_array($properties[2])) {
                $filters = $properties[2];
            } else {
                $name = (string)$properties[2];
                
                if (isset($properties[3])
                    && is_array($properties[3])
                ) {
                    $filters = $properties[3];
                }
            }
        }
        
        if (!isset($filters)) {
            $filters = array();
            preg_match_all('/:([\w\-%]+)/', $route->getPattern(), $params);
            foreach ($params[1] as $param) {
                $filters[$param] = '(\w+)';
            }
        }
        
        $route->setMethods($methods);
        
        if (!empty($filters)) {
            $route->setFilters($filters);
        }
        
        if (isset($name)) {
            if ($this->check($name)) {
                $err_msg = "Route [$name] already exists!"; 
                if (defined('CODESAUR_DEVELOPMENT')
                        && CODESAUR_DEVELOPMENT
                ) {
                    error_log($err_msg);
                }
                throw new Exception($err_msg);
            }
            $this->_routes[$name] = $route;
        } else {
            $this->_routes[] = $route;
        }
    }
    
    public function check(string $routeName): bool
    {
         return isset($this->_routes[$routeName]);
    }
    
    public function match(string $path, string $method): ?Route
    {
        foreach ($this->_routes as $route) {
            if (!in_array($method, $route->getMethods())) {
                continue;
            }
            
            $pattern = '@^' . $route->getRegex() . '/?$@i';
            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }
            
            $params = [];
            if (preg_match_all('/:([\w\-%]+)/', $route->getPattern(), $paramKeys)) {
                if (count($paramKeys[1]) !== (count($matches) - 1)) {
                    continue;
                }
                
                foreach ($paramKeys[1] as $key => $name) {
                    if (isset($matches[$key + 1])) {
                        $params[$name] = $matches[$key + 1];
                    }
                }
            }
            $route->setParameters($params);
            
            return $route;
        }
        
        if ($path === '/' . __FUNCTION__) {
            die(get_class($this));
        }
        
        return null;
    }
    
    public function generate(string $routeName, array $params): array
    {
        try {
            if (!$this->check($routeName)) {
                throw new OutOfRangeException("NO ROUTE: $routeName");
            }
            
            $route = $this->_routes[$routeName];
            
            $paramKeys = array();
            $url = $route->getPattern();
            if ($params && preg_match_all('/:(\w+)/', $url, $paramKeys)) {
                foreach ($paramKeys[1] as $key) {
                    if (isset($params[$key])) {
                        $url = preg_replace('/:(\w+)/', $params[$key], $url, 1);
                    }
                }
            }
            
            return array($url, $route->getMethods());
        } catch (Exception $e) {
            if (defined('CODESAUR_DEVELOPMENT')
                    && CODESAUR_DEVELOPMENT
            ) {
                error_log($e->getMessage());
            }
            
            return array();
        }
    }
    
    public function merge(Router $router)
    {
        $this->_routes = array_merge($this->_routes, $router->getRoutes());
    }
    
    public function getRoutes(): array
    {
        return $this->_routes;
    }
    
    function getRequestMethods(): array
    {
        if (isset($this->requestMethods)) {
            return $this->requestMethods;            
        }
        
        $this->requestMethods = (new Message\RequestMethods)->getMethods();
        return $this->requestMethods;
    }
    
    public function isRoutingFunction(string $methodName): bool
    {
        return $methodName === 'map' || $methodName === 'any'
                || in_array(strtoupper($methodName), $this->getRequestMethods());
    }
}
