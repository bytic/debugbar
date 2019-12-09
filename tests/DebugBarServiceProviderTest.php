<?php

namespace Nip\DebugBar\Tests;

use Nip\DebugBar\DebugBarServiceProvider;
use Nip\DebugBar\StandardDebugBar;

/**
 * Class DebugBarServiceProviderTest
 * @package Nip\DebugBar\Tests
 */
class DebugBarServiceProviderTest extends AbstractTest
{

    public function test_register()
    {
        $provider = new DebugBarServiceProvider();
        $provider->register();

        self::assertInstanceOf(StandardDebugBar::class, $provider->getContainer()->get('debugbar'));
    }

    /**
     * @dataProvider dataBoot
     * @param $config
     * @param $times
     */
    public function testBoot($config, $times)
    {
        $provider = \Mockery::mock(DebugBarServiceProvider::class)->makePartial();
        $provider->shouldAllowMockingProtectedMethods();
        $provider->shouldReceive('getConfigValue')->andReturn($config);
        $provider->shouldReceive('bootDebugBar')->times($times);

        $provider->boot();
        self::assertCount(1, $provider->provides());
    }

    /**
     * @return array
     */
    public function dataBoot()
    {
        return [
            [null, 0],
            ['', 0],
            ['false', 0],
            [false, 0],
            ['true', 1],
            [true, 1],
            [1, 1],
            ['1', 1],
        ];
    }
}
