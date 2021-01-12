<?php namespace codesaur\Http;

use codesaur\Base\Base;

class Router extends Base
{
    private $_routes = array();
    
    public function map(string $path, string $target, array $args = array())
    {
        $route = new Route();
        $route->setPattern($path);
        $route->setControllerAction($target);
        
        $this->mapRoute($route, $args);
    }
    
    public function mapCallback(string $path, callable $callback, ?string $name, array $methods)
    {
        $route = new Route();
        $route->setPattern($path);
        $route->setCallback($callback);
        
        $args = array('name' => $name, 'methods' => $methods, 'filters' => array());
        
        \preg_match_all('/:([\w\-%]+)/', $route->getPattern(), $argumentKeys);
        foreach ($argumentKeys[1] as $name) {
            $args['filters'][$name] = '(\w+)';
        }
        
        $this->mapRoute($route, $args);
    }
    
    public function mapRoute($route, $args)
    {
        if ( ! empty($args['methods'])) {
            $route->setMethods($args['methods']);
        }
        
        if ( ! empty($args['filters'])) {
            $route->setFilters($args['filters']);
        }
        
        if ( ! empty($args['name'])) {
            if ($this->check($args['name']) && DEBUG) {
                \error_log("Route named [{$args['name']}] is found and replaced!");
            }
            $this->_routes[$args['name']] = $route;
        } else {
            $this->_routes[] = $route;
        }
    }
    
    public function match(string $cleanedUrl, string $method) : ?Route
    {
        foreach ($this->_routes as $route) {
            if ( ! \in_array($method, $route->getMethods())) {
                continue;
            }
            
            if ( ! \preg_match('@^' . $route->getRegex() . '/?$@i', $cleanedUrl, $matches)) {
                continue;
            }
            
            $params = [];
            if (\preg_match_all('/:([\w\-%]+)/', $route->getPattern(), $argumentKeys)) {
                if (\count($argumentKeys[1]) !== (\count($matches) - 1)) {
                    continue;
                }
                
                foreach ($argumentKeys[1] as $key => $name) {
                    if (isset($matches[$key + 1])) {
                        $params[$name] = $matches[$key + 1];
                    }
                }
            }
            $route->setParameters($params);
            
            return $route;
        }
        
        if ($cleanedUrl == '/codesaur/' . __FUNCTION__) {
            die($this->getMe());
        }
        
        return null;
    }
    
    public function check(string $routeName) : bool
    {
         return isset($this->_routes[$routeName]);
    }
    
    public function generate(string $routeName, array $params) : array
    {
        try {
            if ( ! $this->check($routeName)) {
                throw new \Exception("NO ROUTE: $routeName");
            }
            
            $route = $this->_routes[$routeName];
            
            $paramKeys = array();
            $url = $route->getPattern();
            if ($params && \preg_match_all('/:(\w+)/', $url, $paramKeys)) {
                foreach ($paramKeys[1] as $key) {
                    if (isset($params[$key])) {
                        $url = \preg_replace('/:(\w+)/', $params[$key], $url, 1);
                    }
                }
            }
            
            return array($url, $route->getMethods());
        } catch (\Exception $e) {
            if (DEBUG) {
                \error_log($e->getMessage());
            }
            
            return array();
        }
    }
    
    public function getRoutes() : array
    {
        return $this->_routes;
    }
    
    public function merge(Router $router)
    {
        $this->_routes = \array_merge($this->_routes, $router->getRoutes());
    }
    
    public function any(string $path, callable $callback, ?string $name = null)
    {
        $this->mapCallback($path, $callback, $name, array('GET', 'POST', 'PUT', 'PATCH', 'DELETE'));
    }
    
    public function get(string $path, callable $callback, ?string $name = null)
    {
        $this->mapCallback($path, $callback, $name, array('GET'));
    }
    
    public function post(string $path, callable $callback, ?string $name = null)
    {
        $this->mapCallback($path, $callback, $name, array('POST'));
    }
    
    public function put(string $path, callable $callback, ?string $name = null)
    {
        $this->mapCallback($path, $callback, $name, array('PUT'));
    }
    
    public function patch(string $path, callable $callback, ?string $name = null)
    {
        $this->mapCallback($path, $callback, $name, array('PATCH'));
    }
    
    public function delete(string $path, callable $callback, ?string $name = null)
    {
        $this->mapCallback($path, $callback, $name, array('DELETE'));
    }
}
