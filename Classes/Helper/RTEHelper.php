<?php

namespace CH\CHCore\Helper;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class RTEHelper
{
    /**
     * @param $content
     * @param $conf
     * @return string
     */
    public function typolinkManipulation($content, $conf): string
    {
        // only proceed if the current link type is a link to typo3 page
        // check if there is already a title provided by the tag,
        // if so, then don't go any further and just return the tag
        if (
            isset($content['TYPE'])
            && $content['TYPE'] === 'page'
            && !preg_match('/title="[^"]+"/', $content['TAG'])
        ) {
            // get the current typo3 link ("t3://page?uid=?") from the register
            // and extract the page id
            $targetRteHref = (string)self::getTypoScriptFrontendController()?->register['rteTargetPageID'];
            preg_match("/uid=(\d+)/", $targetRteHref, $hrefMatches);
            $targetPageUid = $hrefMatches[1] ?? null;
            if (isset($targetPageUid) && is_numeric($targetPageUid)) {
                // with the page id, create a title and remove any undesirable
                // character (like newline). Afterwards add the title to the tag
                $title = $this->createLinkTitleForTargetPage((int)$targetPageUid);
                $title = (string)preg_replace("/(\n+)/", '', $title);
                $content['TAG'] = (string)preg_replace('/>/', "$1 title=\"$title\">", $content['TAG']);
            }
        }

        return $content['TAG'];
    }

    public function createLinkTitleForTargetPage(int $uid): string
    {
        $query = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('pages');
        $query
            ->select('uid', 'title', 'subtitle', 'sys_language_uid')
            ->from('pages', 'p')
            ->where(
                $query->expr()->orX(
                    $query->expr()->eq(
                        'p.uid',
                        $query->quote($uid, \PDO::PARAM_INT)
                    ),
                    $query->expr()->eq(
                        'p.l10n_parent',
                        $query->quote($uid, \PDO::PARAM_INT)
                    )
                )
            );

        // remove all automatically generated conditions and add only the most significant
        $query->getRestrictions()
            ->removeAll()
            ->add(new DeletedRestriction());

        $title = '';
        $result = $query->execute()->fetchAllAssociative();

        if (!empty($result)) {
            $currentLangId = self::getSiteLanguage()?->getLanguageId() ?? 0;
            foreach ($result as $record) {
                // go through each record and check if the respective language is
                // equal the current one and override the title with the translated title
                if ($currentLangId === $record['sys_language_uid']) {
                    $title = $record['subtitle'] ?: $record['title'];
                    break;
                }
            }
        }

        return $title;
    }

    protected static function getRequest(): ?ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? null;
    }

    protected static function getSite(): ?Site
    {
        return self::getRequest()?->getAttribute('site');
    }

    protected static function getSiteLanguage(): ?SiteLanguage
    {
        return self::getRequest()?->getAttribute('language');
    }

    public static function getTypoScriptFrontendController(): ?TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'] ?? null;
    }
}
