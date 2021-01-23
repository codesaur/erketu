<?php declare(strict_types=1);

namespace codesaur\Backup;

class Router
{
    private $_routes = array();
    
    public function __call(string $method, array $properties)
    {
        $route_methods = array('map', 'any', 'get', 'post', 'put', 'patch', 'delete');
        
        if (!in_array($method, $route_methods)) {
            // TODO: throw wrong method name error
            return;
        }
            
        if (is_array($properties[0])) {
            $methods = $properties[0];
            array_shift($properties);
        }
        
        if (empty($properties)
                || empty($properties[1])
        ) {
            // TODO: throw wrong arguments for route error
            return;
        }
        
        $route = new Route();
        $route->setPattern($properties[0]);
        if (is_array($properties[1])
                || is_callable($properties[1])
        ) {            
            $route->setCallback($properties[1]);
        } elseif(is_string($properties[1])) {
            $route->setCallback(array($properties[1], 'index'));
        } else {
            // TODO: throw invalid route callback error
            return;
        }
        
        switch ($method) {
            case 'get':
                if (!isset($methods)) {
                    $methods = array('GET');
                }
                break;
            case 'post':
                if (!isset($methods)) {
                    $methods = array('POST');
                }
                break;
            case 'put':
                if (!isset($methods)) {
                    $methods = array('PUT');
                }
                break;
            case 'patch':
                if (!isset($methods)) {
                    $methods = array('PATCH');
                }
                break;
            case 'delete':
                if (!isset($methods)) {
                    $methods = array('DELETE');
                }
                break;
            case 'any':
                if (!isset($methods)) {
                    $methods = array('GET', 'POST', 'PUT', 'PATCH', 'DELETE');
                }
                break;
        }
        
        if (empty($properties[3])
                || !is_array($properties[3])
        ) {
            $filters = array();
            \preg_match_all('/:([\w\-%]+)/', $route->getPattern(), $params);
            foreach ($params[1] as $name) {
                $filters[$name] = '(\w+)';
            }
        } else {
            $filters = $properties[3];
        }
        
        if (isset($methods)) {
            $route->setMethods($methods);
        }
        
        if (!empty($filters)) {
            $route->setFilters($filters);
        }
        
        if (empty($properties[2])) {
            $this->_routes[] = $route;
        } else {
            if ($this->check($properties[2])
                    && defined('CODESAUR_DEVELOPMENT') && CODESAUR_DEVELOPMENT
            ) {
                \error_log("Route named [{$properties[2]}] is found and being replaced!");
            }
            $this->_routes[$properties[2]] = $route;
        }
    }
    
    public function check(string $routeName): bool
    {
         return isset($this->_routes[$routeName]);
    }
    
    public function match(string $cleanedUrl, string $method): ?Route
    {
        foreach ($this->_routes as $route) {
            if (!in_array($method, $route->getMethods())) {
                continue;
            }
            
            if (!preg_match('@^' . $route->getRegex() . '/?$@i', $cleanedUrl, $matches)) {
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
        
        if ($cleanedUrl == '/codesaur/' . __FUNCTION__) {
            die(get_class($this));
        }
        
        return null;
    }
    
    public function generate(string $routeName, array $params): array
    {
        try {
            if (!$this->check($routeName)) {
                throw new \Exception("NO ROUTE: $routeName");
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
        } catch (\Exception $e) {
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
}
