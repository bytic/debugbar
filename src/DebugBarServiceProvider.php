<?php

namespace Nip\DebugBar;

use Nip\Container\ServiceProvider\AbstractSignatureServiceProvider;
use Nip\Container\ServiceProvider\BootableServiceProviderInterface;
use Nip\DebugBar\Middleware\DebugbarMiddleware;
use Nip\Http\Kernel\Kernel;
use Nip\Http\Kernel\KernelInterface;

/**
 * Class DebugBarServiceProvider
 * @package Nip\DebugBar
 */
class DebugBarServiceProvider extends AbstractSignatureServiceProvider implements BootableServiceProviderInterface
{

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['debugbar'];
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->add(DebugBar::class,StandardDebugBar::class );

        $this->getContainer()->singleton('debugbar', function () {
            $debugbar = $this->getContainer()->get(DebugBar::class);

            return $debugbar;
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // If enabled is null, set from the app.debug value
//        $enabled = $this->app['config']->get('debugbar.enabled');

//        if (is_null($enabled)) {
        $enabled = $this->checkAppDebug();
//        }

        if (!$enabled) {
            return;
        }

        $this->bootDebugBar();
    }

    protected function bootDebugBar()
    {
        $app = $this->getContainer()->get('app');

        /** @var DebugBar $debugBar */
        $debugBar = $app->get('debugbar');
        $debugBar->enable();
        $debugBar->boot();

        $this->registerMiddleware(DebugbarMiddleware::class);
    }

    /**
     * Check the App Debug status
     */
    protected function checkAppDebug()
    {
        $config = $this->getConfigValue();
        if ($config === true || $config == 'true') {
            return true;
        }
        if ($config === 1 || $config == '1') {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function getConfigValue()
    {
        if (!$this->getContainer()->get('config')->has('app.debug')) {
            return false;
        }
        return $this->getContainer()->get('config')->get('app.debug');
    }


    /**
     * Register the Debugbar Middleware
     *
     * @param string $middleware
     */
    protected function registerMiddleware($middleware)
    {
        /** @var Kernel $kernel */
        $kernel = $this->getContainer()->get(KernelInterface::class);
        $kernel->prependMiddleware($middleware);
    }
}
