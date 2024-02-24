<?php

namespace CH\CHCore\EventListeners;

use CH\CHCore\Detections\BrowserDetection;
use SFC\Staticfilecache\Event\CacheRuleFallbackEvent;

class BrowserFallbackEventListener
{
    public function __construct(
        private readonly BrowserDetection $browserDetection
    ) {
    }

    public function __invoke(CacheRuleFallbackEvent $event): void
    {
        if ($this->browserDetection->isBrowserOutdated()) {
            $event->addExplanation(__CLASS__, 'The requesting browser is too old');
            $event->setSkipProcessing(true);
        }
    }
}
