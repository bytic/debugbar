<?php

namespace Nip\DebugBar\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Class AbstractTest
 */
abstract class AbstractTest extends TestCase
{
    protected $object;

    /**
     * @var \UnitTester
     */
    protected $tester;

    public function tearDown(): void
    {
        parent::tearDown();

        if ($container = \Mockery::getContainer()) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }
        \Mockery::close();
    }
}
