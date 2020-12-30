<?php namespace My\Test\App;

class Routing extends \codesaur\Http\Routing
{
    final function getBasicRules() : array
    {
        return array(
            ['', 'My\\Test\\App\\MyController', ['name' => 'home']],
            ['/hello/:user', 'hello@My\\Test\\App\\MyController', ['name' => 'hello', 'filters' => ['user' => '(\w+)']]]
        );
    }
}
