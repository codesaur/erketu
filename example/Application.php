<?php namespace erketu\Example;

use codesaur as single;

class Application extends \codesaur\Base\Application
{
    public function __construct()
    {
        parent::__construct();
        
        $this->route('/', 'erketu\\Example\\RetroController');

        $this->route('/hello/:firstname', 'hello@erketu\\Example\\RetroController', ['name' => 'hello', 'filters' => ['firstname' => '(\w+)']]);

        $this->route('/post-or-put', 'erketu\\Example\\RetroController', ['methods' => ['POST', 'PUT']]);

        $this->any('/home', function() { (new RetroController())->index(); });

        $this->get('/hello/:firstname/:lastname', function($firstname, $lastname) 
        {
           $controller = new RetroController();
           $controller->hello($firstname, $lastname);
        });

        $this->post('/hello/post', function()
        {
            $payload = single::request()->getBodyJson();

            if (empty($payload->firstname)) {
                return single::app()->error('Invalid request!');
            }

            (new RetroController())->hello($payload->firstname, $payload->lastname ?? '');
        });
    }
    
    public function error(string $message, int $status = 404, \Throwable $t = null)
    {
        $controller = new ErrorController();
        $controller->error($message, $status);
    }
}
