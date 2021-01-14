<?php namespace erketu\Example;

use codesaur\Http\Controller;

class ExampleController extends Controller
{
    public function index()
    {
        (new RetroTemplate())->render();
    }
    
    public function response(): ExampleResponse
    {
        return parent::response();
    }
    
    public function hello(string $firstname)
    {
        $user = $firstname;
        if ($this->request()->hasUrlParam('lastname')) {
            $user .= ' ' .  $this->request()->getUrlParam('lastname');
        }

        (new RetroTemplate($user))->render();
    }
    
    public function post_put()
    {
        $payload = $this->request()->getBodyJson();

        if (empty($payload->firstname)) {
            return $this->response()->error('Invalid request!');
        }
        
        $user = $payload->firstname;
        if ( ! empty($payload->lastname)) {
            $user .= " $payload->lastname";
        }

        (new RetroTemplate($user))->render();
    }
}
