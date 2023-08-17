<?php

namespace Leo980\Relay\Tests;

use ArrayIterator;
use Leo980\Relay\Exceptions\EmptyQueueException;
use Leo980\Relay\Exceptions\InvalidMiddlewareException;
use Leo980\Relay\Exceptions\InvalidRequestHandlerException;
use Leo980\Relay\Relay;
use Leo980\Relay\Tests\Fixtures\Middleware;
use Leo980\Relay\Tests\Fixtures\RequestHandler;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

require_once join(DIRECTORY_SEPARATOR, [__DIR__, 'Fixtures', 'Middleware.php']);
require_once join(DIRECTORY_SEPARATOR, [__DIR__, 'Fixtures', 'RequestHandler.php']);

/**
 * @testdox Leo980\Relay\Relay
 */
class RelayTest extends TestCase
{
    public function testCreateRelayWithArrayAsQueue(): void
    {
        $r = new Relay([
            new Middleware('TestField', '456'),
            new Middleware('TestField', '123'),
            new RequestHandler('TestField'),
        ]);

        $this->assertInstanceOf(Relay::class, $r);
    }

    public function testCreateRelayWithIteratorAsQueue(): void
    {
        $i = new ArrayIterator([
            new Middleware('TestField', '456'),
            new Middleware('TestField', '123'),
            new RequestHandler('TestField'),
        ]);

        $r = new Relay($i);
        $this->assertInstanceOf(Relay::class, $r);
    }

    /**
     * @testdox Throw exception if the last item of $queue is not a RequestHandler
     */
    public function testErrorHandling1(): void
    {
        $this->expectException(InvalidRequestHandlerException::class);

        new Relay([
            new Middleware('TestField', '456'),
            new Middleware('TestField', '123'),
            new Middleware('TestField', '789'),
        ]);
    }

    /**
     * @testdox Throw exception if non-tail item of $queue is not a Middleware
     */
    public function testErrorHandling2(): void
    {
        $this->expectException(InvalidMiddlewareException::class);

        new Relay([
            new Middleware('TestField', '456'),
            new RequestHandler('Bang'),
            new Middleware('TestField', '123'),
            new RequestHandler('TestField'),
        ]);
    }

    /**
     * @testdox Throw exception if $queue is empty
     */
    public function testErrorHandling3(): void
    {
        $this->expectException(EmptyQueueException::class);

        new Relay([]);
    }

    /**
     * @testdox Handle request with RequestHandler in $queue only, no Middleware.
     */
    public function testHandleRequest1(): void
    {
        $r = new Relay([new RequestHandler('TestField')]);
        $rs = $r->handle(new ServerRequest(method:'GET', uri:'https://domain.tld'));
        $this->assertSame('', $rs->getHeaderLine('TestField'));
    }

    /**
     * @testdox Handle request with RequestHandler and multiple Middleware(s) in $queue
     */
    public function testHandleRequest2(): void
    {
        $r = new Relay([
            new Middleware('TestField', '789'),
            new Middleware('TestField', '456'),
            new Middleware('TestField', '123'),
            new RequestHandler('TestField'),
        ]);

        $rs = $r->handle(new ServerRequest(method:'GET', uri:'https://domain.tld/'));
        $this->assertSame('789, 456, 123', $rs->getHeaderLine('TestField'));
    }
}
