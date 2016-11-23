#!/usr/bin/env php
<?php

namespace Cormy;

require __DIR__.'/../vendor/autoload.php';

use Cormy\Server\MiddlewareDispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$middleware = function (ServerRequestInterface $request):\Generator {
    // delegate $request to the next request handler, i.e. the $finalHandler below
    $response = (yield $request);

    return $response->withHeader('X-PoweredBy', 'Unicorns');
};

$finalHandler = function (ServerRequestInterface $request):ResponseInterface {
    return new \Zend\Diactoros\Response();
};

// create a dispatcher
$dispatcher = new MiddlewareDispatcher($middleware, $finalHandler);

// dispatch a request
$response = $dispatcher(new \Zend\Diactoros\ServerRequest());

exit($response->getHeader('X-PoweredBy')[0] === 'Unicorns' ? 0 : 1);
