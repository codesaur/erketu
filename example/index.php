<?php

/* DEV: v9.2021.01.08
 * 
 * This is an example script!
 */

use codesaur\Base\Application;
use My\Test\App\RetroController;

$autoload = require_once '../vendor/autoload.php';
$autoload->addPsr4('My\\Test\\App\\', \dirname(__FILE__));

$application = new Application('My\\Test\\App\\');

$application->route('/', "My\\Test\\App\RetroController");

$application->route('/hello/:firstname', "hello@My\\Test\\App\RetroController", ['name' => 'hello', 'filters' => ['firstname' => '(\w+)']]);

$application->route('/post-or-put', "My\\Test\\App\RetroController", ['methods' => ['POST', 'PUT']]);

$application->any('/home', function() { (new RetroController())->index(); });

$application->get('/hello/:firstname/:lastname', function($firstname, $lastname) 
{
   $controller = new RetroController();
   $controller->hello($firstname, $lastname);
});

$application->post('/hello/post', function()
{
    $payload = codesaur::request()->getBodyJson();
    
    if (empty($payload->firstname)) {
        return codesaur::app()->error('Invalid request!');
    }
    
    (new RetroController())->hello($payload->firstname, $payload->lastname ?? '');
});

\codesaur::start($application);
