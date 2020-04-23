<?php

namespace Nip\DebugBar\Tests\Traits;

use Nip\DebugBar\DebugBar;
use Nip\DebugBar\Tests\AbstractTest;

/**
 * Class BootableTest
 * @package Nip\DebugBar\Tests\Traits
 */
class BootableTest extends AbstractTest
{
    public function test_boot_only_once()
    {
        /** @var DebugBar $debugBar */
        $debugBar = \Mockery::mock(DebugBar::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $debugBar->shouldReceive('doBoot')->once();

        $debugBar->boot();
        $debugBar->boot();
        $debugBar->boot();
    }
}
