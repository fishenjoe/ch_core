<?php

namespace CH\CHCore\Middleware;

use CH\CHCore\Detections\BrowserDetection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * Check the support of the current requesting browser and redirect
 * either to the 'browser-update' page or carry on like before.
 */
class BrowserDetectionCheck implements MiddlewareInterface
{
    public function __construct(
        private readonly BrowserDetection $browserDetection
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        /** @var Site $site */
        $site = $request->getAttribute('site');
        /** @var PageArguments $routing */
        $routing = $request->getAttribute('routing');

        if (
            $site !== null
            && $routing !== null
            && $this->browserDetection->isBrowserOutdated()
        ) {
            $browserUpdatePageId = $site->getAttribute('browserUpdatePageId');
            if (
                is_numeric($browserUpdatePageId)
                && $routing->getPageId() !== (int)$browserUpdatePageId
            ) {
                return new RedirectResponse(
                    $site->getRouter()->generateUri($browserUpdatePageId)
                );
            }
        }

        return $handler->handle($request);
    }
}
