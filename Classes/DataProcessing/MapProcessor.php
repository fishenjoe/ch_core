<?php

namespace CH\CHCore\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Maps the given array with another array of specific mappings `key`<>`value`.
 * If no mapping exist, use an optional fallback value.
 */
class MapProcessor implements DataProcessorInterface
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

        // The input array process
        $inputArray = $cObj->stdWrapValue('inputArray', $processorConfiguration);
        if (empty($inputArray)) {
            return $processedData;
        }

        $inputValue = $processedData[$inputArray];
        if (!is_array($inputValue)) {
            return $processedData;
        }

        // Set the target variable
        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration);
        if (empty($targetVariableName)) {
            return $processedData;
        }

        // Set the target variable
        $fallback = $cObj->stdWrapValue('fallback', $processorConfiguration, null);

        // get the map array and process
        $mapArray = $processorConfiguration['map.'];
        $tempMap = array_reduce(
            $inputValue,
            static function ($carry, $value) use ($mapArray, $fallback) {
                $carry[$value] = $mapArray[$value] ?? $fallback;
                return $carry;
            },
            []
        );

        $processedData[$targetVariableName] = $tempMap;

        return $processedData;
    }
}
