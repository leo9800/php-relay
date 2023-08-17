<?php

namespace Leo980\Relay;

use Leo980\Relay\Exceptions\EmptyQueueException;
use Leo980\Relay\Exceptions\InvalidMiddlewareException;
use Leo980\Relay\Exceptions\InvalidRequestHandlerException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Wrapping a PSR-15 request handler and multiple (optional) 
 * PSR-15 middlewares into a PSR-15 request handler.
 */
class Relay implements RequestHandlerInterface
{
    /**
     * @var RequestHandlerInterface Top-level middleware wrapper
     */
    private RequestHandlerInterface $request_handler;

    /**
     * @param iterable<MiddlewareInterface|RequestHandlerInterface> $queue
     */
    public function __construct(iterable $queue)
    {
        if (!is_array($queue))
            $queue = iterator_to_array($queue);

        if (empty($queue))
            throw new EmptyQueueException();

        $request_handler = array_pop($queue);

        if (!($request_handler instanceof RequestHandlerInterface))
            throw new InvalidRequestHandlerException();

        foreach (array_reverse($queue) as $middleware) {
            if (!($middleware instanceof MiddlewareInterface))
                throw new InvalidMiddlewareException();

            $request_handler = new MiddlewareWrapper($middleware, $request_handler);
        }

        $this->request_handler = $request_handler;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->request_handler->handle($request);
    }
}
