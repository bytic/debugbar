<?php

namespace Nip\DebugBar\Traits;

use DebugBar\Bridge\MonologCollector;
use Monolog\Logger as MonologLogger;
use Nip\DebugBar\Formatter\MonologFormatter;

/**
 * Trait HasMonologCollector
 * @package Nip\DebugBar\Traits
 */
trait HasMonologCollector
{
    /**
     * @return bool
     * @throws \DebugBar\DebugBarException
     */
    public function doBootMonologCollector()
    {
        if (app()->has(MonologCollector::class) === false) {
            return false;
        }

        $monologCollector = app(MonologCollector::class);
        $this->addMonologCollector($monologCollector);

        return true;
    }

    /**
     * @param MonologLogger $monolog
     * @throws \DebugBar\DebugBarException
     */
    public function addMonolog(MonologLogger $monolog)
    {
        $collector = new MonologCollector($monolog);
        $this->addMonologCollector($collector);
    }

    /**
     * @param MonologCollector $collector
     */
    protected function addMonologCollector(MonologCollector $collector)
    {
        $collector->setFormatter(new MonologFormatter());
        $this->addCollector($collector);
    }
}
