<?php

$GLOBALS['SiteConfiguration']['site']['columns']['browserUpdatePageId'] = [
    'label' => "'Browser-Update' page ID",
    'description' =>
        "The page id of the 'Browser-Update' page. It will be used in the BrowserDetectionCheck Middleware, " .
        'to redirect automatically to that page, if the current UserAgent is too old.',
    'config' => [
        'type' => 'input',
        'eval' => 'int',
    ],
];

$GLOBALS['SiteConfiguration']['site']['palettes']['default']['showitem'] = str_replace(
    'websiteTitle',
    'websiteTitle, browserUpdatePageId, ',
    $GLOBALS['SiteConfiguration']['site']['palettes']['default']['showitem']
);
