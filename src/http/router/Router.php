<?php declare(strict_types=1);

namespace codesaur\Http\Router;

use Exception;
use ArgumentCountError;
use InvalidArgumentException;
use BadMethodCallException;
use BadFunctionCallException;
use OutOfRangeException;

use codesaur\Http\Message\RequestMethods;

class Router
{
    private $_routes = array();
    
    public function __call(string $method, array $properties) : Route
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
        
        $pattern = $properties[0];
        
        if (is_array($properties[1])
                || is_callable($properties[1])
        ) {
            $callback = $properties[1];
        } elseif (is_string($properties[1])) {
            $callback = array($properties[1], 'index');
        } else {
            throw new InvalidArgumentException("Invalid callback on route pattern [$pattern]!");
        }
        
        $filters = array();
        $specificFilters = array(
            'uint:' => '(\d+)',
            'int:' => '(-?\d+)',
            'float:' => '(-?\d+|-?\d*\.\d+)',
        );
        $specificFilterMatch = implode('|', array_keys($specificFilters));
        $match_pattern = '/\{(' . $specificFilterMatch . ')?([\w\-%]+)\}/';
        preg_match_all($match_pattern, $pattern, $params);
        foreach ($params[2] as $index => $param) {
            $filters[$param] = $specificFilters[$params[1][$index]] ?? '(\w+)';
        }
        $path = str_replace(array_keys($specificFilters), '', $pattern);
        
        $route = new Route($methods, $path, $filters, $callback);
        
        $this->_routes[] = $route;
        
        return end($this->_routes);
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
            if (preg_match_all('/\{([\w\-%]+)\}/', $route->getPattern(), $paramKeys)) {
                if (count($paramKeys[1]) !== (count($matches) - 1)) {
                    continue;
                }
                
                foreach ($paramKeys[1] as $key => $name) {
                    if (isset($matches[$key + 1])) {
                        if ($route->getFilters()[$name] === '(\w+)') {
                            $params[$name] = $matches[$key + 1];
                        } elseif ($route->getFilters()[$name] === '(-?\d+|-?\d*\.\d+)') {
                            $params[$name] = (float)$matches[$key + 1];
                        } else {
                            $params[$name] = (int)$matches[$key + 1];
                        }
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
            if ($params && preg_match_all('/\{(\w+)\}/', $url, $paramKeys)) {
                foreach ($paramKeys[1] as $key) {
                    if (isset($params[$key])) {
                        $url = preg_replace('/\{(\w+)\}/', $params[$key], $url, 1);
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
        
        $this->requestMethods = (new RequestMethods())->getMethods();
        return $this->requestMethods;
    }
    
    public function isRoutingFunction(string $methodName): bool
    {
        return $methodName === 'map' || $methodName === 'any'
                || in_array(strtoupper($methodName), $this->getRequestMethods());
    }
}
