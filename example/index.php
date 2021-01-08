<?php namespace My\Test\App;

/* DEV: v9.2021.01.08
 * 
 * This is an example script!
 */

use codesaur as single;
use codesaur\Base\Application;

$namespace = __NAMESPACE__ . '\\';

$autoload = require_once '../vendor/autoload.php';
$autoload->addPsr4($namespace, \dirname(__FILE__));

$application = new Application($namespace);

$application->route('/', "{$namespace}RetroController");

$application->route('/hello/:firstname', "hello@{$namespace}RetroController", ['name' => 'hello', 'filters' => ['firstname' => '(\w+)']]);

$application->route('/post-or-put', "{$namespace}RetroController", ['methods' => ['POST', 'PUT']]);

$application->any('/home', function () { (new RetroController())->index(); });

$application->get('/hello/:firstname/:lastname', function ($firstname, $lastname) 
{
   $controller = new RetroController();
   $controller->hello($firstname, $lastname);
});

$application->post('/hello/post', function ()
{
    $payload = single::request()->getBodyJson();
    
    if (empty($payload->firstname)) {
        return single::app()->error('Invalid request!');
    }
    
    (new RetroController())->hello($payload->firstname, $payload->lastname ?? null);
});

single::start($application);
