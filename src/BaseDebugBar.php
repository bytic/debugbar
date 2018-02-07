<?php

namespace Nip\DebugBar;

use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;

/**
 * Class StandardDebugBar
 * @package Nip\DebugBar
 */
class BaseDebugBar extends DebugBar
{
    /**
     * @throws \DebugBar\DebugBarException
     */
    public function doBoot()
    {
        $this->addCollector(new PhpInfoCollector());
        $this->addCollector(new MessagesCollector());
        $this->addCollector(new RequestDataCollector());
        $this->addCollector(new TimeDataCollector());
        $this->addCollector(new MemoryCollector());
    }
}
