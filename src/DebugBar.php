<?php

namespace Nip\DebugBar;

use DebugBar\Bridge\MonologCollector;
use DebugBar\DebugBar as DebugBarGeneric;
use Monolog\Logger as MonologLogger;
use Nip\DebugBar\Formatter\MonologFormatter;
use Nip\Http\Response\JsonResponse;
use Nip\Http\Response\Response;
use Nip\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class DebugBar
 * @package Nip\DebugBar
 */
abstract class DebugBar extends DebugBarGeneric
{
    use Traits\Bootable;

    /**
     * True when enabled, false disabled an null for still unknown
     *
     * @var bool
     */
    protected $enabled = false;


    /**
     * Enable the DebugBar and boot, if not already booted.
     */
    public function enable()
    {
        $this->enabled = true;

        $this->boot();
    }

    /**
     * Disable the DebugBar
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * @param MonologLogger $monolog
     * @throws \DebugBar\DebugBarException
     */
    public function addMonolog(MonologLogger $monolog)
    {
        $collector = new MonologCollector($monolog);
        $collector->setFormatter(new MonologFormatter());
        $this->addCollector($collector);
    }

    /**
     * Modify the response and inject the debugbar (or data in headers)
     *
     * @param  Request|ServerRequestInterface $request
     * @param  Response|ResponseInterface $response
     * @return Response
     */
    public function modifyResponse(Request $request, ResponseInterface $response)
    {
        if (!$this->isEnabled() || $response instanceof JsonResponse) {
            return $response;
        }

        return $this->injectDebugBar($response);
    }

    /**
     * Check if the DebugBar is enabled
     * @return boolean
     */
    public function isEnabled()
    {
        if ($this->enabled === null) {
            $this->enabled = true;
        }

        return $this->enabled;
    }

    /**
     * Injects the web debug toolbar
     * @param Response $response
     */
    public function injectDebugBar(ResponseInterface $response)
    {
        $content = $response->getContent();

        $renderer = $this->getJavascriptRenderer();
        $renderedContent = $this->generateAssetsContent().$renderer->render();

        $pos = strripos($content, '</body>');
        if (false !== $pos) {
            $content = substr($content, 0, $pos).$renderedContent.substr($content, $pos);
        } else {
            $content = '<body>'.$content.'</body>'.$renderedContent;
        }
        // Update the new content and reset the content length
        $response->setContent($content);
        $response->headers->remove('Content-Length');
    }

    /**
     * @return mixed|string
     */
    protected function generateAssetsContent()
    {
        $renderer = $this->getJavascriptRenderer();
        ob_start();
        echo '<style>';
        echo $renderer->dumpCssAssets();
        echo '</style>';
        echo '<script type="text/javascript">';
        echo $renderer->dumpJsAssets();
        echo '</script>';
        echo '<script type="text/javascript">jQuery.noConflict(true);</script>';
        $content = ob_get_clean();

        if (defined('FONTS_URL')) {
            $content = str_replace('../fonts/', FONTS_URL, $content);
        } elseif (function_exists('asset') && function_exists('app')) {
            $content = str_replace('../fonts', asset('/compiled/fonts/'), $content);
        }

        return $content;
    }
}
