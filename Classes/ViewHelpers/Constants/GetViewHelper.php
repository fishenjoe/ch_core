<?php

namespace CH\CHCore\ViewHelpers\Constants;

use CH\CHCore\Services\ConstantService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class GetViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'name',
            'string',
            'The name of the requested constant',
            true
        );
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        if (!is_string($arguments['name'])) {
            return '';
        }

        return GeneralUtility::makeInstance(ConstantService::class)
            ->getConstant($arguments['name']);
    }
}
