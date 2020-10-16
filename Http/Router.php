<?php namespace codesaur\Http;

use codesaur\Base\Base;

class Router extends Base
{
    private $_routes = array();
    
    public function map(string $routeUrl, string $target, array $args)
    {
        $route = new Route();
        $route->setControllerAction($target);
        
        if (empty($route->getController()) ||
                \ctype_space($route->getController())) {
            return;
        }

        $route->setPattern($routeUrl);

        if (isset($args['methods'])) {
            $methods = \explode(',', $args['methods']);
            $route->setMethods($methods);
        }
        
        if (isset($args['filters'])) {
            $route->setFilters($args['filters']);
        }
        
         if (isset($args['name'])) {
            if ( ! $this->check($args['name'])) {
                $this->_routes[$args['name']] = $route;
            } else {
                $this->_routes[$args['name'] . '-' . \count($this->_routes)] = $route;
            }
        } else {
            $this->_routes[] = $route;
        }
    }
    
    public function match(string $cleanedUrl, string $method)
    {
        foreach ($this->_routes as $idx => $route) {
            if ( ! \in_array($method, $route->getMethods())) {
                continue;
            }
            
            if ( ! \preg_match('@^' . $route->getRegex() . '/?$@i', $cleanedUrl, $matches)) {
                continue;
            }
            
            $params = [];
            if (\preg_match_all('/:([\w\-%]+)/', $route->getPattern(), $argumentKeys)) {
                $argumentKeys = $argumentKeys[1];
                
                if (\count($argumentKeys) !== (\count($matches) - 1)) {
                    continue;
                }
                
                foreach ($argumentKeys as $key => $name) {
                    if (isset($matches[$key + 1])) {
                        $params[$name] = $matches[$key + 1];
                    }
                }
            }
            $route->setParameters($params);
            
            $route->name = $idx;
            
            return $route;
        }
        
        if ($cleanedUrl == '/' . __FUNCTION__) {
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
                $paramKeys = $paramKeys[1];
                foreach ($paramKeys as $key) {
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
