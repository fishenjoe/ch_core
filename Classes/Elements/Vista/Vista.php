<?php

namespace CH\CHCore\Elements\Vista;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Resource\FileCollector;

class Vista
{
    protected int $curPageId;
    protected int $curLangId;
    protected array $config = [];

    public function __construct(
        protected TypoScriptService $tsService,
        protected Context $context,
        protected ContentObjectRenderer $cObj,
    ) {
    }

    public function setGlobalProperties(int $curLangId, int $curPageId): void
    {
        $this->curLangId = $curLangId;
        $this->curPageId = $curPageId;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    protected function getCategoriesFromVistaSwitch(): array
    {
        if (empty($this->config['switchPid'])) {
            return [];
        }

        // build SQL query to get the desired values from the vista switch
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tt_content');
        $qb
            ->select('selected_categories')
            ->from('tt_content')
            ->where(
                $qb->expr()->eq(
                    'pid',
                    $qb->createNamedParameter($this->config['switchPid'], \PDO::PARAM_INT)
                )
            )
            ->setMaxResults(1);

        return explode(',', (string)$qb->execute()->fetchOne());
    }

    public function process(): array
    {
        $this->config['selectedCategories'] = $this->getCategoriesFromVistaSwitch();

        // build SQL query to get the vista's
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tt_content');
        $qb
            ->select(
                'tt_content.uid',
                'tt_content.pid',
                'cat.title as cat_title',
                'cat.uid as cat_uid',
                'tt_content.sorting'
            )
            ->from('tt_content')
            ->join(
                'tt_content',
                'sys_category',
                'cat',
                $qb->expr()->eq(
                    'cat.uid',
                    $qb->quoteIdentifier('tt_content.selected_categories')
                )
            )
            ->where(
                $qb->expr()->eq(
                    'tt_content.pid',
                    $qb->createNamedParameter(
                        $this->curPageId,
                        \PDO::PARAM_INT,
                        ':pid'
                    )
                ),
                $qb->expr()->eq(
                    'tt_content.selected_categories',
                    $qb->createNamedParameter(
                        '0',
                        \PDO::PARAM_STR,
                        ':cat'
                    )
                ),
                $qb->expr()->eq(
                    'tt_content.colPos',
                    $qb->createNamedParameter(
                        $this->config['colPos'],
                        \PDO::PARAM_INT
                    )
                ),
                $qb->expr()->eq(
                    'tt_content.CType',
                    $qb->createNamedParameter(
                        'ch_vista_element',
                        \PDO::PARAM_STR,
                        ':ctype'
                    )
                ),
                $qb->expr()->in(
                    'tt_content.sys_language_uid',
                    $qb->createNamedParameter(
                        [$this->curLangId, -1],
                        Connection::PARAM_INT_ARRAY
                    )
                )
            );

        $recordsCollection = [];

        do {
            $again = false;
            $rowsElements = [];

            // iterate through the `selected_categories` and make a query for each of them
            if (!empty($this->config['selectedCategories'])) {
                foreach ($this->config['selectedCategories'] as $category) {
                    $qb->setParameter('cat', $category);
                    $rowsPerCategory = $qb->execute()->fetchAssociative();
                    if (!empty($rowsPerCategory)) {
                        $rowsElements[] = $rowsPerCategory;
                    }
                }
            } else {
                $rowsPerCategory = $qb->execute()->fetchAssociative();
                if (!empty($rowsPerCategory)) {
                    $rowsElements[] = $rowsPerCategory;
                }
            }

            if (!empty($rowsElements)) {
                $sorting = [];
                foreach ($rowsElements as $sortKey => $sortRow) {
                    $sorting[$sortKey] = $sortRow['sorting'];
                }
                array_multisort($sorting, $rowsElements);

                foreach ($rowsElements as $key => $record) {
                    $recordContentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
                    $recordContentObjectRenderer->start($record, 'tt_content');

                    // get the image/video references
                    /** @var FileCollector $fileCollector */
                    $fileCollector = GeneralUtility::makeInstance(FileCollector::class);
                    $fileCollector->addFilesFromRelation(
                        'tt_content',
                        'assets',
                        $recordContentObjectRenderer->data
                    );

                    $record['assets'] = $fileCollector->getFiles();
                    $recordsCollection[$key] = ['data' => $record];
                }
            }

            if (empty($recordsCollection)) {
                // slide up the rootline if no records are found under the current page
                foreach (
                    array_reverse(explode(',', $this->cObj->getSlidePids($this->curPageId, [])))
                    as $currentPid
                ) {
                    $this->curPageId = (int)$currentPid;
                    // check if we are not at the root page already
                    if ($this->curPageId !== 0) {
                        $qb->setParameter('pid', $this->curPageId);
                        $again = true;
                    }
                }
            }
        } while ($again === true);

        // construct the result values
        return [
            'config' => $this->config,
            'records' => $recordsCollection ?? [],
        ];
    }
}
