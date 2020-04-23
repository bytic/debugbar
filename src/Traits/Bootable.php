<?php

namespace Nip\DebugBar\Traits;

/**
 * Trait Bootable
 * @package Nip\DebugBar\Traits
 */
trait Bootable
{

    /**
     * True when booted.
     *
     * @var bool
     */
    protected $booted = false;

    public function boot()
    {
        if ($this->booted) {
            return;
        }

        $this->doBoot();
        $this->booted = true;
    }

    /**
     * @return void
     */
    abstract public function doBoot();
}
