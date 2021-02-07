<?php

namespace erketu\Example;

/* DEV: v9.2021.01.08
 * 
 * This is an example script!
 */

use Psr\Http\Message\ServerRequestInterface;

use codesaur\Http\Message\ServerRequest;
use codesaur\Http\Application;

$autoload = require_once '../vendor/autoload.php';
$autoload->addPsr4(__NAMESPACE__ . '\\', \dirname(__FILE__));

codesaur_set_environment();

$request = new ServerRequest();
$request->initFromGlobal();

$application = new Application();

$application->use(new ExampleError());

$application->any('/', ExampleController::class);


$application->use(new ExampleRouter());

$application->get('/home', function () { (new RetroTemplate())->render(); })->name('home');

$application->get('/hello/{firstname}/{lastname}', function (ServerRequestInterface $req) 
{
    $name = "{$req->getAttribute('firstname')} {$req->getAttribute('lastname')}";
    
    (new RetroTemplate($name))->render();
});

$application->post('/hello/post', function (ServerRequestInterface $req)
{
    $payload = $req->getParsedBody();

    if (empty($payload['firstname'])) {
        throw new \Error('Invalid request!');
    }

    $user = $payload['firstname'];
    if (!empty($payload['lastname'])) {
        $user .= " {$payload['lastname']}";
    }

    (new RetroTemplate($user))->render();
});

$application->get('/float/{float:number}', [ExampleController::class, 'float']);

$application->get('/sum/{int:a}/{uint:b}', function (ServerRequestInterface $req)
{
    $a = $req->getAttribute('a');
    $b = $req->getAttribute('b');

    $sum = $a + $b;

    var_dump($a, $b, $sum);
    
    echo "$a + $b = $sum";
});

$application->handle($request);
