<?php namespace erketu\Example;

use codesaur\Http\Request;
use codesaur\Http\Response;

class Application extends \codesaur\Base\Application
{
    public function __construct(
            Request $request, Response $response)
    {
        parent::__construct($request, $response);
        
        $this->map('/', 'erketu\\Example\\RetroController', ['name' => 'index']);

        $this->map('/hello/:firstname', 'hello@erketu\\Example\\RetroController', ['name' => 'hello', 'filters' => ['firstname' => '(\w+)']]);

        $this->map('/post-or-put', 'erketu\\Example\\RetroController', ['methods' => ['POST', 'PUT']]);

        $this->any('/home', function(Request $req, Response $res)
        {
            (new RetroController($this))->index();
        });

        $this->get('/hello/:firstname/:lastname', function(Request $req, Response $res) 
        {
           $controller = new RetroController($this);
           $controller->hello($req->params->firstname, $req->params->lastname);
        });

        $this->post('/hello/post', function(Request $req, Response $res)
        {
            $payload = $req->getBodyJson();

            if (empty($payload->firstname)) {
                return $res->error('Invalid request!');
            }

            (new RetroController($this))->hello($payload->firstname, $payload->lastname ?? '');
        });
    }
}
