<?php

namespace CH\CHCore\Generator;

use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Frontend\Event\ModifyHrefLangTagsEvent;
use TYPO3\CMS\Seo\HrefLang\HrefLangGenerator as HrefLangGeneratorOriginal;

/**
 * Overrides the behaviour of the one from seo package.
 * In contrast to, it doesn't treat the 'x-default' tag as
 * the default lang. The primary way is english and only if that
 * is empty, consider the first defined language [zero index].
 *
 * Furthermore, it trims a potential slash from the end of the url.
 */
class HrefLangGenerator extends HrefLangGeneratorOriginal
{
    public function __invoke(ModifyHrefLangTagsEvent $event): void
    {
        $hrefLangs = $event->getHrefLangs();
        if ((int)$this->getTypoScriptFrontendController()->page['no_index'] === 1) {
            return;
        }

        $this->cObj->setRequest($event->getRequest());
        $languages = $this->languageMenuProcessor->process($this->cObj, [], [], []);
        $site = $this->getTypoScriptFrontendController()->getSite();
        $siteLanguage = $this->getTypoScriptFrontendController()->getLanguage();
        $pageId = (int)$this->getTypoScriptFrontendController()->id;

        foreach ($languages['languagemenu'] as $language) {
            if (!empty($language['link']) && $language['hreflang']) {
                $page = $this->getTranslatedPageRecord($pageId, $language['languageId'], $site);
                // do not set hreflang if a page is not translated explicitly
                if (empty($page)) {
                    continue;
                }
                // do not set hreflang when canonical is set explicitly
                if (!empty($page['canonical_link'])) {
                    continue;
                }

                $href = $this->getAbsoluteUrl($language['link'], $siteLanguage);
                $hrefLangs[$language['hreflang']] = $href;
            }
        }

        if (count($hrefLangs) > 1) {
            $href = $hrefLangs['en'] ?? null;
            if (empty($href)) {
                $href = $this->getAbsoluteUrl($languages['languagemenu'][0]['link'], $siteLanguage);
            }

            $hrefLangs['x-default'] = $href;
        }

        $event->setHrefLangs($hrefLangs);
    }

    protected function getAbsoluteUrl(string $url, SiteLanguage $siteLanguage): string
    {
        $uri = new Uri($url);
        $newUrl = null;
        if (empty($uri->getHost())) {
            $newUrl = $siteLanguage->getBase()->withPath(rtrim($uri->getPath(), '/'));

            if ($uri->getQuery()) {
                $newUrl = $newUrl->withQuery($uri->getQuery());
            }
        }

        return (string)($newUrl ?? $url);
    }
}
