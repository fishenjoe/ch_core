<?php

namespace CH\CHCore\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Will redirect the user to the requested target of the shortcut with the *correct* status code:
 *  - `307` for redirects to 'rather random pages' (first subpage, random subpage, parent page)
 *  - `301` for redirects to 'a well-defined page' (explicitly selected page)
 */
class ShortcutRedirect implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        /** @var TypoScriptFrontendController $controller */
        $controller = $request->getAttribute('frontend.controller');

        $shortcutMode = (int)($controller?->originalShortcutPage['shortcut_mode'] ?? 0);
        if (
            !(
                ($shortcutMode === PageRepository::SHORTCUT_MODE_FIRST_SUBPAGE)
                || ($shortcutMode === PageRepository::SHORTCUT_MODE_RANDOM_SUBPAGE)
                || ($shortcutMode === PageRepository::SHORTCUT_MODE_PARENT_PAGE)
            )
        ) {
            try {
                $redirectToUri = $controller->getRedirectUriForShortcut($request);
            } catch (ImmediateResponseException $e) {
                return $e->getResponse();
            }

            if (
                $redirectToUri !== null
                && $redirectToUri !== (string)$request->getUri()
            ) {
                return new RedirectResponse(
                    $redirectToUri,
                    301,    // Moved Permanently
                    ['X-Redirect-By' => 'CH Shortcut Redirect'],
                );
            }
        }

        return $handler->handle($request);
    }
}
