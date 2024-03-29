<?php

namespace Nip\DebugBar;

use DebugBar\DebugBar as DebugBarGeneric;
use Nip\Http\Request;
use Nip\Http\Response\JsonResponse;
use Nip\Http\Response\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class DebugBar
 * @package Nip\DebugBar
 */
abstract class DebugBar extends DebugBarGeneric
{
    use Traits\Bootable;
    use Traits\HasMonologCollector;

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
     * Modify the response and inject the debugbar (or data in headers)
     *
     * @param  Request|ServerRequestInterface $request
     * @param  Response|ResponseInterface $response
     * @return Response
     */
    public function modifyResponse(ServerRequestInterface $request, ResponseInterface $response)
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
        $renderedContent = $this->generateAssetsContent() . $renderer->render();

        $pos = strripos($content, '</body>');
        if (false !== $pos) {
            $content = substr($content, 0, $pos) . $renderedContent . substr($content, $pos);
        } else {
            $content = '<body>' . $content . '</body>' . $renderedContent;
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

        $content = str_replace('../fonts/', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/fonts/', $content);
//        if (defined('FONTS_URL')) {
//            $content = str_replace('../fonts/', FONTS_URL, $content);
//        } elseif (function_exists('asset') && function_exists('app') && app()->has('url')) {
//            $content = str_replace('../fonts', asset('/compiled/fonts/'), $content);
//        }

        return $content;
    }
}
