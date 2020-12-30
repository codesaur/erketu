<?php namespace My\Test\App;

/* DEV: v8.2020.12.29
 * 
 * This is test script!
 */

use codesaur\Base\Application;

$autoload = require_once '../vendor/autoload.php';
$autoload->addPsr4('My\\Test\\App\\', \dirname(__FILE__));

\codesaur::start(new Application(array('/' => 'My\\Test\\App\\')));
