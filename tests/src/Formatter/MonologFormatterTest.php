<?php

namespace Nip\DebugBar\Tests\Formatter;

use Nip\DebugBar\Formatter\MonologFormatter;
use Nip\DebugBar\Tests\AbstractTest;

/**
 * Class MonologFormatterTest
 * @package Nip\DebugBar\Tests\Formatter
 */
class MonologFormatterTest extends AbstractTest
{
    public function test_format()
    {
        $formatter = new MonologFormatter();

        $record = require TEST_FIXTURE_PATH . '/records/simple.php';
        self::assertStringStartsWith($record['level_name'], $formatter->format($record));
    }
}
