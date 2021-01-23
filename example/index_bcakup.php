<?php

namespace erketu\Example;

/* DEV: v9.2021.01.08
 * 
 * This is an example script!
 */

use codesaur\Base\Application;

use codesaur\Http\Response;
use codesaur\Http\ServerRequest;

$autoload = require_once '../vendor/autoload.php';
$autoload->addPsr4(__NAMESPACE__ . '\\', \dirname(__FILE__));

codesaur_set_environment();

$application = new Application();

$application->map('/', ExampleController::class);

$application->use(new ExampleRouter());

$application->any('/home', function ()
{
    (new RetroTemplate())->render();
});

$application->get('/hello/:firstname/:lastname', function ($req, $res) 
{
    $res->render(new RetroTemplate("{$req->params->firstname} {$req->params->lastname}"));
});

$application->post('/hello/post', function (ServerRequest $req, Response $res)
{
    $payload = $req->getBodyJson();

    if (empty($payload->firstname)) {
        return $res->error('Invalid request!');
    }

    $user = $payload->firstname;
    if (!empty($payload->lastname)) {
        $user .= " $payload->lastname";
    }

    $res->render(new RetroTemplate($user));
});

$response = $application->handle(new ServerRequest());
$response->send();
