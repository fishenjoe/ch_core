<?php

namespace CH\CHCore\PageTitle;

use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class SubTitlePageTitleProvider extends AbstractPageTitleProvider
{
    public function __construct()
    {
        $this->title = (string)self::getTypoScriptFrontendController()->page['subtitle'];
    }

    public static function getTypoScriptFrontendController(): ?TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'] ?? null;
    }
}
