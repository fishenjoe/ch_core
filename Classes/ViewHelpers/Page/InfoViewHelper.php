<?php

namespace CH\CHCore\ViewHelpers\Page;

use CH\DataRetriever\Repository\PageRepository;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Get page information with following possibilities:
 * - infos from current page or another one
 * - infos from current language or another one
 * - infos from a specific table field or all fields
 *
 * ### EXAMPLE
 *
 * #### Default call
 *
 * ```
 *  <ch:page.info field="title" pageUid="1" langUid="1"/>
 *      <!-- e.g. "Home" -->
 * ```
 *
 * #### Get some data from pages...
 *
 * ```
 *  {ch:page.info(field: '') | f:debug()}
 *      <!-- debug all fields in current language -->
 *
 *  {ch:page.info(field: 'title', langUid: '1') | f:debug()}
 *      <!-- debug field 'title' in language with id '1' -->
 *
 *  {ch:page.info(field: 'categories') | f:debug()}
 *      <!-- debug field 'categories' in current language -->
 * ```
 */
class InfoViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'pageUid',
            'int',
            'If specified, this UID will be used to fetch page data instead of using the current page.',
            false,
            0
        );
        $this->registerArgument(
            'field',
            'string',
            'If specified, only this field will be returned/assigned instead of the complete page record.',
            false,
            'uid'
        );
        $this->registerArgument(
            'langUid',
            'int',
            'If specified, this UID will be used to select the desired language instead of the current one.',
            false
        );
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): mixed {
        $tsfe = self::getTyposcriptFrontendController();
        if (null === $tsfe && $arguments['pageUid'] === 0) {
            return null;
        }

        $pageUid = (int) ($arguments['pageUid'] === 0 ? $tsfe->id : $arguments['pageUid']);
        $pageField = (string)$arguments['field'];
        $pageLangUid = (int) (
            $arguments['langUid']
            ?? self::getSiteLanguage()?->getLanguageId()
            ?? 0
        );

        $data = GeneralUtility::makeInstance(PageRepository::class)
            ->getOneByLangAndUid($pageLangUid, $pageUid);

        # return output, either specific field or complete data
        if ($pageField !== '') {
            if (isset($data[$pageField])) {
                return $data[$pageField];
            }
        } else {
            return $data;
        }

        return null;
    }

    protected static function getTyposcriptFrontendController(): ?TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'] ?? null;
    }

    protected static function getRequest(): ?ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? null;
    }

    protected static function getSiteLanguage(): ?SiteLanguage
    {
        return self::getRequest()?->getAttribute('language');
    }
}
