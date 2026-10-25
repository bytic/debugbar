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

    protected function generateAssetsContent(): string
    {
        $renderer = $this->getJavascriptRenderer();

        return '<style>' . $renderer->dumpCssAssets(null, false) . '</style>'
            . '<script type="text/javascript">' . $renderer->dumpJsAssets(null, false) . '</script>';
    }
}
