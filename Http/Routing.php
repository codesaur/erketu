<?php namespace codesaur\Http;

abstract class Routing implements RoutingInterface
{
    public function match(Router &$router, Request $request)
    {
        $this->collect($router);
        
        return $this->entry($router->match($request->getCleanUrl(), $request->getMethod()));
    }

    function entry($route)
    {
        return $route;
    }
    
    function collect(Router &$router)
    {
        $methods = \get_class_methods($this);
        
        foreach ($methods as $name) {
            if (\substr($name, 0, 3) != 'get') {
                continue;
            }
            
            foreach ($this->$name() as $rule) {
                if (isset($rule[0])) {
                    $router->map($rule[0], $rule[1] ?? '', $rule[2] ?? []);
                }
            }
        }
    }
}
