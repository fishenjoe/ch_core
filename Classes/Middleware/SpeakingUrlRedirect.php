<?php

namespace CH\CHCore\Middleware;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * Redirect from a direct request "/index.php?id=1&L=0" or "/?id=2" to
 * something like "/speaking/url/path".
 *
 * The only requirement for the trigger is the 'id' argument.
 * If not given, or the special argument 'ch_RAW_URL' is present,
 * the whole conversion will be ignored.
 */
class SpeakingUrlRedirect implements MiddlewareInterface
{
    public function __construct(
        private readonly SiteFinder $siteFinder
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $params = $request->getQueryParams();

        if (
            !empty($params)
            && !isset($params['CH_RAW_URL'])
            && !empty($params['id'])
        ) {
            try {
                $id = (int)($params['id']);
                $languageId = (int)($params['L'] ?? 0);
                $filteredParams = array_filter(
                    $params,
                    // remove reserved params `id` and `L` from `params` array
                    static fn ($key) => !($key === 'id' || $key === 'L'),
                    ARRAY_FILTER_USE_KEY
                );

                $url = $this->siteFinder
                    ->getSiteByPageId($id)
                    ->getRouter()
                    ->generateUri($id, array_merge($filteredParams, [ '_language' => $languageId ]));
            } catch (SiteNotFoundException|InvalidArgumentException) {
                return $handler->handle($request);
            }

            return new RedirectResponse(
                $url,
                302,
                ['X-Redirect-By' => 'CH Speaking Url Redirect'],
            );
        }

        return $handler->handle($request);
    }
}
