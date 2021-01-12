<?php

/* DEV: v9.2021.01.08
 * 
 * This is an example script!
 */

use codesaur\Http\Request;
use codesaur\Http\Response;
use codesaur\Base\Application;

use erketu\Example\ExampleRouter;
use erketu\Example\RetroTemplate;
use erketu\Example\ExampleResponse;

$autoload = require_once '../vendor/autoload.php';
$autoload->addPsr4('erketu\\Example\\', \dirname(__FILE__));

$application = new Application();

$application->map('/', 'erketu\\Example\\ExampleController');

$application->merge(new ExampleRouter());

$application->any('/home', function()
{
    (new RetroTemplate())->render();
});

$application->get('/hello/:firstname/:lastname', function($req, $res) 
{
    $res->render(new RetroTemplate("{$req->params->firstname} {$req->params->lastname}"));
});

$application->post('/hello/post', function(Request $req, Response $res)
{
    $payload = $req->getBodyJson();

    if (empty($payload->firstname)) {
        return $res->error('Invalid request!');
    }

    $template = new RetroTemplate($payload->firstname);
    if ( ! empty($payload->lastname)) {
        $template->enhance('user', " $payload->lastname");
    }

    $res->render($template);
});

$application->handle((new Request())->initFromGlobal(), new ExampleResponse());
