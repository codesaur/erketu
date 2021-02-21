<?php declare(strict_types=1);

namespace codesaur\Http\Router;

use ArgumentCountError;
use InvalidArgumentException;
use BadMethodCallException;
use BadFunctionCallException;

use Psr\Http\Message\UriInterface;

use codesaur\Http\Message\RequestMethods;
use codesaur\Http\Message\Uri;

class Router implements RouterInterface
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
        
        $route = new Route($methods, $pattern, $callback);

        $filters = array();
        preg_match_all(self::PARAMS_FILTER, $pattern, $params);        
        foreach ($params[2] as $index => $param) {
            switch ($params[1][$index]) {
                case self::PARAM_INT: $filters[$param] = self::FILTER_INT; break;
                case self::PARAM_UNSIGNED_INT: $filters[$param] = self::FILTER_UNSIGNED_INT; break;
                case self::PARAM_FLOAT: $filters[$param] = self::FILTER_FLOAT; break;
                default: $filters[$param] = self::FILTER_STRING;
            }
        }
        $route->setFilters($filters);
        
        $this->_routes[] = $route;
        
        return end($this->_routes);
    }
    
    public function getRouteByName(string $name): ?Route
    {
        foreach ($this->getRoutes() as $route) {
            if ($route->getName() === $name) {
                return $route;
            }            
        }
        
        return null;
    }
    
    public function match(string $pattern ,string $method): ?Route
    {
        foreach ($this->_routes as $route) {
            if (!in_array($method, $route->getMethods())) {
                continue;
            }
            
            $route_regex = $route->getRegex(self::PARAMS_FILTER);
            if (!preg_match($route_regex, $pattern, $matches)) {
                continue;
            }
        
            $params = [];
            if (preg_match_all(self::PARAMS_FILTER, $route->getPattern(), $paramKeys)) {
                if (count($paramKeys[2]) !== (count($matches) - 1)) {
                    continue;
                }
                foreach ($paramKeys[2] as $key => $name) {
                    if (isset($matches[$key + 1])) {
                        $filter = $route->getFilters()[$name];
                        if ($filter === self::FILTER_STRING
                        ) {
                            $params[$name] = $matches[$key + 1];
                        } elseif ($filter === self::FILTER_FLOAT
                        ) {
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
        
        if ($pattern === '/' . __FUNCTION__) {
            die(get_class($this));
        }
        
        return null;
    }
    
    public function generate(string $routeName, array $params): ?UriInterface
    {
        $route = $this->getRouteByName($routeName);            
        if (!$route instanceof Route) {
            if (defined('CODESAUR_DEVELOPMENT')
                    && CODESAUR_DEVELOPMENT
            ) {
                error_log("NO ROUTE: $routeName");
            }

            return null;
        }

        $paramKeys = array();
        $pattern = $route->getPattern();
        if ($params && preg_match_all(self::PARAMS_FILTER, $pattern, $paramKeys)) {
            foreach ($paramKeys[2] as $index => $key) {
                if (isset($params[$key])) {                        
                    $filter = $route->getFilters()[$key];
                    switch ($filter) {
                        case self::FILTER_FLOAT: 
                            if (!is_numeric($params[$key])) {
                                throw new InvalidArgumentException("[$pattern] Route parameter expected to be float value!");
                            }
                            break;
                        case self::FILTER_INT: 
                            if (!is_int($params[$key])) {
                                throw new InvalidArgumentException("[$pattern] Route parameter expected to be integer value!");
                            }
                            break;
                        case self::FILTER_UNSIGNED_INT:
                            $is_uint = filter_var($params[$key], FILTER_VALIDATE_INT, array('options' => array('min_range' => 0)));
                            if ($is_uint === false) {
                                throw new InvalidArgumentException("[$pattern] Route parameter expected to be unsigned integer value!");
                            }
                            break;
                    }

                    $pattern = preg_replace('/\{' . $paramKeys[1][$index] . '(\w+)\}/', $params[$key], $pattern, 1);
                }
            }
        }

        $uri = new Uri();
        $uri->setPath($pattern);

        return $uri;
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
