<?php namespace codesaur\Http;

use codesaur\Base\Base;

class Router extends Base
{
    private $_routes = array();
    
    private function addRouteArgs($route, $args)
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
    
    public function map(string $path, string $target, array $args)
    {
        $route = new Route();
        $route->setControllerAction($target);
        
        if (empty($route->getController()) ||
                \ctype_space($route->getController())) {
            throw new \Exception("Invalid route target for pattern: $path");
        }

        $route->setPattern($path);
        
        $this->addRouteArgs($route, $args);
    }
    
    public function mapCallback(string $path, callable $callback, $name, array $methods)
    {
        $route = new Route();
        $route->setPattern($path);
        $route->setCallback($callback);
        
        $args = array('name' => $name, 'methods' => $methods, 'filters' => array());
        
        \preg_match_all('/:([\w\-%]+)/', $route->getPattern(), $argumentKeys);
        foreach ($argumentKeys[1] as $name) {
            $args['filters'][$name] = '(\w+)';
        }
        
        $this->addRouteArgs($route, $args);
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
}
