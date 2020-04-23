<?php

namespace Nip\DebugBar\Tests\Middleware;

use Nip\DebugBar\BaseDebugBar;
use Nip\DebugBar\Middleware\DebugbarMiddleware;
use Nip\DebugBar\Tests\AbstractTest;
use Nip\Http\Response\Response;
use Nip\Http\ServerMiddleware\Dispatcher;
use Nip\Request;

/**
 * Class DebugbarMiddlewareTest
 * @package Nip\DebugBar\Tests\Middleware
 */
class DebugbarMiddlewareTest extends AbstractTest
{

    public function testDebugNotEnabled()
    {
        $debugbar = new BaseDebugBar();

        $dispatcher = new Dispatcher(
            [
                new DebugbarMiddleware($debugbar),
                function () {
                    return (new Response())->setContent('test');
                },
            ]
        );

        /** @var Response $response */
        $response = $dispatcher->dispatch(new Request());

        self::assertInstanceOf(Response::class, $response);
        self::assertSame('test', $response->getContent());
    }

    public function testDebugEnabled()
    {
        $debugbar = new BaseDebugBar();
        $debugbar->enable();

        $dispatcher = new Dispatcher(
            [
                new DebugbarMiddleware($debugbar),
                function () {
                    return (new Response())->setContent('test');
                },
            ]
        );

        /** @var Response $response */
        $response = $dispatcher->dispatch(new Request());

        self::assertInstanceOf(Response::class, $response);

        $content = $response->getContent();
        self::assertStringStartsWith('<body>test</body>', $content);
        self::assertStringContainsString('phpdebugbar', $content);
    }
}
