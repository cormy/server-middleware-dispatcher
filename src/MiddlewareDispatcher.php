<?php

namespace Cormy\Server;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Cormy PSR-7 server middleware dispatcher.
 */
class MiddlewareDispatcher implements RequestHandlerInterface
{
    /**
     * @var callable|MiddlewareInterface
     */
    protected $middleware;

    /**
     * @var callable|RequestHandlerInterface
     */
    protected $finalHandler;

    /**
     * Create a Cormy PSR-7 server middleware dispatcher.
     *
     * @param callable|MiddlewareInterface     $middleware
     * @param callable|RequestHandlerInterface $finalHandler
     */
    public function __construct(callable $middleware, callable $finalHandler)
    {
        $this->middleware = $middleware;
        $this->finalHandler = $finalHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request):ResponseInterface
    {
        $middleware = $this->middleware;
        $finalHandler = $this->finalHandler;
        $current = $middleware($request);

        while ($current->valid()) {
            $nextRequest = $current->current();

            try {
                $nextResponse = $finalHandler($nextRequest);
                $current->send($nextResponse);
            } catch (Throwable $exception) {
                $current->throw($exception);
            }
        }

        return $current->getReturn();
    }
}
