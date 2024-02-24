<?php

namespace CH\CHCore\DataProcessing;

use CH\CHCore\Elements\Vista\Vista;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Gets the data of the wanted vista, configured via typoscript
 */
class VistaProcessor implements DataProcessorInterface
{
    protected int $curPageId;
    protected int $curLangId;
    protected array $config = [];

    public function __construct(
        private Context $context,
        private TypoScriptService $tsService
    ) {
        $this->curLangId = $this->context->getAspect('language')->get('id');
        $this->curPageId = $GLOBALS['TSFE']->id;
    }

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        // check for possible if statement first
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        // the switchPid to query
        $this->config['switchPid'] = (int)$cObj->stdWrapValue('switchPid', $processorConfiguration);

        // get the desired colPos of the vista element. (default: 1)
        $this->config['colPos'] = (int)(
            !empty($processedColPos = $cObj->stdWrapValue('colPos', $processorConfiguration))
                ? $processedColPos
                : 1
        );

        $vista = GeneralUtility::makeInstance(
            Vista::class,
            $this->tsService,
            $this->context,
            $cObj
        );
        $vista->setGlobalProperties($this->curLangId, $this->curPageId);
        $vista->setConfig($this->config);

        $as = (string)$cObj->stdWrapValue('as', $processorConfiguration, 'vistaData');
        $processedData[$as] = $vista->process();

        return $processedData;
    }
}
