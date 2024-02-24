<?php

namespace CH\CHCore\Generator;

use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Seo\Canonical\CanonicalGenerator as CanonicalGeneratorOriginal;
use TYPO3\CMS\Seo\Event\ModifyUrlForCanonicalTagEvent;

/**
 * Overrides the behaviour of the one from seo package.
 * It only trims a potential slash from the end of the url.
 */
class CanonicalGenerator extends CanonicalGeneratorOriginal
{
    public function generate(): string
    {
        if ($this->typoScriptFrontendController->config['config']['disableCanonical'] ?? false) {
            return '';
        }

        $event = $this->eventDispatcher->dispatch(new ModifyUrlForCanonicalTagEvent(''));
        $href = $event->getUrl();

        if (empty($href) && (int)$this->typoScriptFrontendController->page['no_index'] === 1) {
            return '';
        }

        if (empty($href)) {
            // 1) Check if page has canonical URL set
            $href = $this->checkForCanonicalLink();
        }
        if (empty($href)) {
            // 2) Check if page show content from other page
            $href = $this->checkContentFromPid();
        }
        if (empty($href)) {
            // 3) Fallback, create canonical URL
            $href = $this->checkDefaultCanonical();
        }

        // trim a potential slash from the end of the url
        $uri = new Uri($href);
        $url = $uri->withPath(rtrim($uri->getPath(), '/'));
        $href = (string)$url;

        if (!empty($href)) {
            $canonical = '<link ' . GeneralUtility::implodeAttributes([
                    'rel' => 'canonical',
                    'href' => $href,
                ], true) . '/>' . LF;
            $this->typoScriptFrontendController->additionalHeaderData[] = $canonical;
            return $canonical;
        }
        return '';
    }
}
