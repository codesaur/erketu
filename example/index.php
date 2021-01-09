<?php

/* DEV: v9.2021.01.08
 * 
 * This is an example script!
 */

$autoload = require_once '../vendor/autoload.php';
$autoload->addPsr4('erketu\\Example\\', \dirname(__FILE__));

\codesaur::start(new erketu\Example\Application());
