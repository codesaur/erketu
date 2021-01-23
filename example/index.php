<?php

namespace erketu\Example;

/* DEV: v9.2021.01.08
 * 
 * This is an example script!
 */

$autoload = require_once '../vendor/autoload.php';
$autoload->addPsr4(__NAMESPACE__ . '\\', \dirname(__FILE__));

codesaur_set_environment();

$request = new \codesaur\Http\Message\ServerRequest();

$application = new \codesaur\Http\Application();
$response = $application->handle($request);
$response->send();
