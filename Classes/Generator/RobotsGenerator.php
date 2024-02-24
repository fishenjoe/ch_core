<?php

namespace CH\CHCore\Generator;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

class RobotsGenerator
{
    /**
     * Generates dynamically the robots.txt file
     */
    public function render(): string
    {
        // static - twitter
        $outputText = "User-Agent: Twitterbot\n";
        $outputText .= "Disallow: \n";
        $outputText .= "\n";

        // static - all agents (default)
        $outputText .= "User-Agent: *\n";
        $outputText .= "Allow: /\n";
        $outputText .= "\n";

        // dynamic - generate sitemap urls
        $outputText .= $this->generateSitemapUrls();

        return $outputText;
    }

    /**
     * Generate all sitemap URLs (foreach found language) and return them
     */
    private function generateSitemapUrls(): string
    {
        $output = '';
        $site = self::getSite();
        if ($site !== null && method_exists($site, 'getAllLanguages')) {
            foreach ($site->getAllLanguages() as $language) {
                if ($language->enabled()) {
                    // ensure that at the end of the uri is a slash
                    $uri = rtrim($language->getBase(), '/') . '/sitemap.xml';
                    $output .= "Sitemap: {$uri}\n";
                }
            }
        }

        return $output;
    }

    protected static function getRequest(): ?ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? null;
    }

    protected static function getSite(): ?Site
    {
        return self::getRequest()?->getAttribute('site');
    }
}
