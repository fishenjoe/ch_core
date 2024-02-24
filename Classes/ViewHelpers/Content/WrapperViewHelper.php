<?php

namespace CH\CHCore\ViewHelpers\Content;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class WrapperViewHelper extends AbstractTagBasedViewHelper
{
    use UnwantedArgumentsTrait;

    protected string $wrapperFirst = 'div';
    protected string $wrapperIfNoHeaderSet = 'div';
    protected string $wrapperDefault = 'section';

    public static int $hitCounter = 0;
    public static int $hitCounterAfterFirstHeader = 0;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute(
            'header',
            'array',
            'An array of header strings',
            true
        );
    }

    public function initialize(): void
    {
        $this->hideUnwantedArguments(['header']);
        parent::initialize();
        $this->restoreUnwantedArguments();
        $this->tag->forceClosingTag(true);
        $this->defineTag($this->tag);
    }

    /**
     * Define the tag, which the renderer should finally render
     */
    protected function defineTag(TagBuilder $tag): void
    {
        // increment counter by one for each wrapper call
        self::$hitCounter++;

        // increment another counter by one for each wrapper call
        // if at least <h1> is set
        if (HeadingViewHelper::$wasFirstHeaderSet) {
            self::$hitCounterAfterFirstHeader++;
        }

        // if not at least one header string is given, or it's not the first record,
        // wrap everything with the default tag
        if (self::$hitCounter === 1) {
            $tagType = $this->wrapperFirst;
        } elseif (!empty(array_filter((array)$this->arguments['header']))) {
            $tagType = $this->wrapperDefault;
        } else {
            $tagType = $this->wrapperIfNoHeaderSet;
        }

        $tag->setTagName($tagType);
    }

    /**
     * Render the Tag with content inside
     * @return string
     */
    public function render(): string
    {
        // render tag content
        $this->tag->setContent($this->renderChildren());
        // render the final tag itself
        return $this->tag->render();
    }
}
