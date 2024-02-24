<?php

namespace CH\CHCore\ViewHelpers\Content;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class CountCharViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'text',
            'string',
            'The text from where the single characters were counted',
            false,
            ''
        );
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        return strlen($arguments['text'] ?? '');
    }
}
