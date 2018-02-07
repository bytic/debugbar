<?php

namespace Nip\DebugBar;

use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use Monolog\Logger as Monolog;
use Nip\DebugBar\DataCollector\RouteCollector;
use Nip\DebugBar\Traits\HasQueryCollector;

/**
 * Class StandardDebugBar
 * @package Nip\DebugBar
 */
class StandardDebugBar extends DebugBar
{
    use HasQueryCollector;

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
        $this->addCollector(new RouteCollector());

        $this->doBootQueryCollector();

        if (app()->has(Monolog::class)) {
            $monolog = app(Monolog::class);
            $this->addMonolog($monolog);
        } else {
            $this->addCollector(new ExceptionsCollector());
        }
    }
}
