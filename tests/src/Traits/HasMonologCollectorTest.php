<?php

namespace Nip\DebugBar\Tests\Traits;

use DebugBar\Bridge\Monolog\MonologCollector;
use Monolog\Logger;
use Nip\DebugBar\BaseDebugBar;
use Nip\DebugBar\Tests\AbstractTest;

class HasMonologCollectorTest extends AbstractTest
{
    public function testAddMonologUsesCurrentBridgeCollector(): void
    {
        $debugbar = new BaseDebugBar();

        $debugbar->addMonolog(new Logger('test'));

        self::assertInstanceOf(MonologCollector::class, $debugbar->getCollector('monolog'));
    }
}
