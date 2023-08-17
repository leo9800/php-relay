<?php

namespace Leo980\Relay\Tests\Fixtures;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Nyholm\Psr7\Response;

class RequestHandler implements RequestHandlerInterface
{
    public function __construct(private string $k)
    {

    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response(
            status:200,
            reason:'OK',
            version:'1.1',
            headers:[$this->k => $request->getHeaderLine($this->k)],
        );
    }
}
