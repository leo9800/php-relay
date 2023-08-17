<?php

namespace Leo980\Relay\Tests;

require_once join(DIRECTORY_SEPARATOR, [__DIR__, 'Fixtures', 'Middleware.php']);
require_once join(DIRECTORY_SEPARATOR, [__DIR__, 'Fixtures', 'RequestHandler.php']);

use Leo980\Relay\MiddlewareWrapper;
use Leo980\Relay\Tests\Fixtures\Middleware;
use Leo980\Relay\Tests\Fixtures\RequestHandler;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Leo980\Relay\MiddlewareWrapper
 */
class MiddlewareWrapperTest extends TestCase
{
    public MiddlewareWrapper $mw;

    public function setUp(): void
    {
        $this->mw = new MiddlewareWrapper(
            new Middleware('TestField', '123'),
            new RequestHandler('TestField'),
        );
    }

    public function testChainingMiddlewares(): void
    {
        $r = $this->mw->handle(new ServerRequest(
            method:'GET',
            uri:'https://domain.tld/',
        ));

        $this->assertSame('123', $r->getHeaderLine('TestField'));
    }
}
