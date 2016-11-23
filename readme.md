# Cormy Server Middleware Dispatcher [![Build Status](https://travis-ci.org/cormy/server-middleware-dispatcher.svg?branch=master)](https://travis-ci.org/cormy/server-middleware-dispatcher) [![Coverage Status](https://coveralls.io/repos/cormy/server-middleware-dispatcher/badge.svg?branch=master&service=github)](https://coveralls.io/github/cormy/server-middleware-dispatcher?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cormy/server-middleware-dispatcher/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/cormy/server-middleware-dispatcher/?branch=master)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/653860df-7903-4711-98b3-c60e349f1e65/big.png)](https://insight.sensiolabs.com/projects/653860df-7903-4711-98b3-c60e349f1e65)

> :nut_and_bolt: Cormy [PSR-7](http://www.php-fig.org/psr/psr-7) server middleware dispatcher


## Install

```
composer require cormy/server-middleware-dispatcher
```


## Usage

```php
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
```


## API

### `Cormy\Server\MiddlewareDispatcher implements Cormy\Server\RequestHandlerInterface`

#### `MiddlewareDispatcher::__construct`

```php
/**
 * Create a Cormy PSR-7 server middleware dispatcher.
 *
 * @param callable|MiddlewareInterface     $middleware
 * @param callable|RequestHandlerInterface $finalHandler
 */
public function __construct(callable $middleware, callable $finalHandler)
```

#### Inherited from [`Cormy\Server\RequestHandlerInterface::__invoke`](https://github.com/cormy/server-request-handler)

```php
/**
 * Process an incoming server request and return the response.
 *
 * @param ServerRequestInterface $request
 *
 * @return ResponseInterface
 */
public function __invoke(ServerRequestInterface $request):ResponseInterface;
```


## Related

* [Cormy\Server\Onion](https://github.com/cormy/onion) – Onion style PSR-7 **middleware stack** using generators
* [Cormy\Server\Bamboo](https://github.com/cormy/bamboo) – Bamboo style PSR-7 **middleware pipe** using generators
* [Cormy\Server\RequestHandlerInterface](https://github.com/cormy/server-request-handler) – Common interfaces for PSR-7 server request handlers
* [Cormy\Server\MiddlewareInterface](https://github.com/cormy/server-middleware) – Common interfaces for Cormy PSR-7 server middlewares


## License

MIT © [Michael Mayer](http://schnittstabil.de)
