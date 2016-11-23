<?php

namespace Cormy\Server;

use Exception;
use Cormy\Server\Helpers\CounterMiddleware;
use Cormy\Server\Helpers\FinalHandler;
use Cormy\Server\Helpers\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequest;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    use \VladaHejda\AssertException;

    public function testCormyInterfaceShouldBeValid()
    {
        $finalHandler = new FinalHandler('Final!');
        $middleware = new CounterMiddleware(0);

        $sut = new Dispatcher($middleware, $finalHandler);
        $response = $sut(new ServerRequest());

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('Final!0', (string) $response->getBody());
    }

    public function testCallbacksShouldBeValid()
    {
        $finalHandler = function (ServerRequestInterface $request):ResponseInterface {
            return new Response('Final!');
        };
        $middleware = function (ServerRequestInterface $request) {
            static $index = 0;

            $response = yield $request;
            $response->getBody()->write((string) $index++);

            return $response;
        };

        $sut = new Dispatcher($middleware, $finalHandler);
        $response = $sut(new ServerRequest());

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('Final!0', (string) $response->getBody());
    }

    public function testMiddlewaresCanHandleFinalHandlerExceptions()
    {
        $finalHandler = function (ServerRequestInterface $request):ResponseInterface {
            throw new Exception('Oops, something went wrong!', 500);
        };
        $middleware = function (ServerRequestInterface $request) {
            try {
                $response = (yield $request);
            } catch (Exception $e) {
                return new Response('Catched: '.$e->getMessage(), $e->getCode());
            }
        };

        $sut = new Dispatcher($middleware, $finalHandler);
        $response = $sut(new ServerRequest());

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('Catched: Oops, something went wrong!', (string) $response->getBody());
        $this->assertSame(500, $response->getStatusCode());
    }

    public function testMiddlewareCallerHaveToHandleFinalHandlerExceptions()
    {
        $finalHandler = function (ServerRequestInterface $request):ResponseInterface {
            throw new Exception('Oops, something went wrong!', 500);
        };
        $middleware = function (ServerRequestInterface $request) {
            $response = (yield $request);

            return $response;
        };

        $sut = new Dispatcher($middleware, $finalHandler);

        $this->assertException(function () use ($sut) {
            $sut(new ServerRequest());
        }, Exception::class, 500, 'Oops, something went wrong!');
    }
}
