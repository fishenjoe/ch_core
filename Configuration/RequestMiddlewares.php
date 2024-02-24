<?php

use CH\CHCore\Middleware\BrowserDetectionCheck;
use CH\CHCore\Middleware\ShortcutRedirect;
use CH\CHCore\Middleware\SpeakingUrlRedirect;

/**
 * An array consisting of implementations of middlewares for a middleware stack to be registered
 *
 *  'stackname' => [
 *      'middleware-identifier' => [
 *         'target' => classname or callable
 *         'before/after' => array of dependencies
 *      ]
 *   ]
 */

return [
    'frontend' => [
        'ch/ch-core/speaking-url-redirect' => [
            'target' => SpeakingUrlRedirect::class,
            'after' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
            ],
            'before' => [
                'typo3/cms-frontend/content-length-headers',
            ],
        ],
        'ch/ch-core/shortcut-redirect' => [
            'target' => ShortcutRedirect::class,
            'before' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
            ],
        ],
        'ch/ch-core/browser-detection-check' => [
            'target' => BrowserDetectionCheck::class,
            'before' => [
                'ch/ch-core/shortcut-redirect',
            ],
        ],
    ],
];
