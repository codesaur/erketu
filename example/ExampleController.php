<?php

namespace erketu\Example;

use Error;

use codesaur\Http\Controller;

class ExampleController extends Controller
{
    public function index()
    {
        (new RetroTemplate())->render();
    }
    
    public function hello(string $firstname)
    {
        $user = $firstname;
        if (isset($this->getRequest()->getQueryParams()['lastname'])) {
            $user .= ' ' .  $this->getRequest()->getQueryParams()['lastname'];
        }
        
        (new RetroTemplate($user))->render();
    }
    
    public function post_put()
    {
        $payload = $this->getRequest()->getParsedBody();

        if (empty($payload['firstname'])) {
            throw new Error('Invalid request!');
        }
        
        $user = $payload['firstname'];
        if (!empty($payload['lastname'])) {
            $user .= " {$payload['lastname']}";
        }

        (new RetroTemplate($user))->render();
    }
    
    public function float(float $number)
    {
        var_dump($number);
    }
}
