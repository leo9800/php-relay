<?php

namespace Leo980\Relay;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Wrap a PSR-15 middleware and its next PSR-15 request handler 
 * into a PSR-15 request handler.
 * This enables chaining multiple middlewares with a request 
 * handler at tail.
 */
class MiddlewareWrapper implements RequestHandlerInterface
{
    /**
     * @param MiddlewareInterface     $middleware      Middleware to be wrapped
     * @param RequestHandlerInterface $request_handler Next request handler
     */
    public function __construct(
        private MiddlewareInterface $middleware,
        private RequestHandlerInterface $request_handler,
    )
    {

    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->middleware->process($request, $this->request_handler);
    }
}