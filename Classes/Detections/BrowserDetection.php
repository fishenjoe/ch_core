<?php

namespace CH\CHCore\Detections;

use CH\CHCore\Traits\Cache;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WhichBrowser\Parser;

class BrowserDetection
{
    use Cache;

    /**
     * Check the current requesting browser from the given HTTP_USER_AGENT string
     * and return either TRUE if a specific browser (and version) is matched, or FALSE otherwise.
     */
    public function isBrowserOutdated(?string $userAgent = null): bool
    {
        $userAgent = (string)($userAgent ?? GeneralUtility::getIndpEnv('HTTP_USER_AGENT'));
        $parser = self::getFromCache(
            'default',
            $userAgent,
            static function () use ($userAgent): Parser {
                $parser = new Parser();
                $parser->analyse($userAgent);
                return $parser;
            }
        );

        $browser = $parser->browser;

        return ($browser->getVersion() !== '') &&
            (
                (
                    // all IE browsers
                    'Internet Explorer' === $browser->getName()
                ) || (
                    // all SAFARI browsers lower than version 8
                    'Safari' === $browser->getName() && $browser->version->is('<', '8')
                )
            );
    }
}
