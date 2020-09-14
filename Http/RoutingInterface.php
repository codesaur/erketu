<?php namespace codesaur\Http;

interface RoutingInterface
{
    public function entry($route);

    public function match(Router &$router, Request $request);
    public function collect(Router &$router);
}
