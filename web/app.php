<?php

use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__.'/../app/autoload.php';
include_once __DIR__.'/../var/bootstrap.php.cache';


// Enable APC for autoloading to improve performance.
// You should change the ApcClassLoader first argument to a unique prefix
// in order to prevent cache key conflicts with other applications
// also using APC.


$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();


$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
