<?php

namespace CH\CHCore\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Returns the item of an array, by a key which is given as a priority list.
 * The first matched key will be used.
 */
class ArrayPriorityProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        // The field name to process
        $inputArrayName = $cObj->stdWrapValue('inputArray', $processorConfiguration);
        if (empty($inputArrayName)) {
            return $processedData;
        }

        $inputArray = $processedData[$inputArrayName];
        if (!is_array($inputArray)) {
            return $processedData;
        }

        // Set the target variable
        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration);
        if (empty($targetVariableName)) {
            return $processedData;
        }

        // get the priority list
        $priorityList = $cObj->stdWrapValue('priorityList', $processorConfiguration);
        $priorityList = GeneralUtility::trimExplode(',', $priorityList, true);

        $tempResult = null;
        foreach ($priorityList as $prioItem) {
            if (array_key_exists($prioItem, $inputArray)) {
                $tempResult = $inputArray[$prioItem];
                break;
            }
        }

        $processedData[$targetVariableName] = $tempResult;

        return $processedData;
    }
}
