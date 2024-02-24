<?php

namespace CH\CHCore\ViewHelpers\Content;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class RecordNumberViewHelper extends AbstractViewHelper
{
    protected ContentObjectRenderer $contentObject;
    protected ConfigurationManagerInterface $configurationManager;

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
        $this->contentObject = $configurationManager->getContentObject();
    }

    public function initializeArguments(): void
    {
        $this->registerArgument(
            'return',
            'string',
            'Specify the result of what the ViewHelper should return',
            false,
            'number'
        );
    }

    public function render(): string
    {
        $instruction = $this->arguments['return'];

        return match ($instruction) {
            'isFirst' => (WrapperViewHelper::$hitCounter === 1) ? 1 : 0,
            'number' => WrapperViewHelper::$hitCounter,
            default => throw new \UnexpectedValueException(
                "Unknown value '{$instruction}' for return parameter in " . __CLASS__
            ),
        };
    }
}
