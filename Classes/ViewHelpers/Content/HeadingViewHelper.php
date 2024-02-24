<?php

namespace CH\CHCore\ViewHelpers\Content;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class HeadingViewHelper extends AbstractTagBasedViewHelper
{
    use UnwantedArgumentsTrait;

    protected array $headerTypes = [
        'header' => 1,
        'subheader' => 2,
        'thirdheader' => 3,
    ];
    protected array $headerTags = [
        1 => 'h1',
        2 => 'h2',
        3 => 'h3',
        4 => 'h4',
    ];
    protected string $headerTagDefault = 'span';
    public static bool $wasFirstHeaderSet = false;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute(
            'headerType',
            'string',
            'The type of the tag'
        );
        $this->registerTagAttribute(
            'classByTag',
            'array',
            'An array of class-by-tags mappings'
        );
    }

    public function initialize(): void
    {
        $this->hideUnwantedArguments(['headerType', 'classByTag']);
        parent::initialize();
        $this->restoreUnwantedArguments();
        $this->tag->forceClosingTag(true);
        $this->defineTag($this->tag);
        $this->specifyAdditionalClassesByTagType($this->tag);
    }

    protected function defineTag(TagBuilder $tag): void
    {
        $mappedHeaderTags = [];
        foreach ($this->headerTypes as $name => $typeNumber) {
            $mappedHeaderTags[$name] =
                $this->headerTags[
                    (WrapperViewHelper::$hitCounterAfterFirstHeader > 0)
                        ? ($typeNumber + 1)
                        : $typeNumber
                ]
                ?? $this->headerTagDefault;
        }

        if (
            self::$wasFirstHeaderSet === false
            && ($mappedHeaderTags['header'] ?? '') === $this->headerTags[1]
        ) {
            self::$wasFirstHeaderSet = true;
        }

        $headerType = $this->arguments['headerType'] ?? '';
        $tag->setTagName(
            $mappedHeaderTags[$headerType] ?? 'div'
        );
    }

    protected function specifyAdditionalClassesByTagType(TagBuilder $tag): void
    {
        $tagClassMap = $this->arguments['classByTag'];
        if (!empty($tagClassMap[$tag->getTagName()])) {
            $tag->addAttribute(
                'class',
                trim($tag->getAttribute('class') . ' ' . $tagClassMap[$tag->getTagName()])
            );
        }
    }

    /**
     * Render the Tag with content inside
     */
    public function render(): string
    {
        // render tag content
        $this->tag->setContent($this->renderChildren());
        // render the final tag itself
        return $this->tag->render();
    }
}
