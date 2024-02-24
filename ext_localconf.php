<?php

defined('TYPO3') or die();

use CH\CHCore\Generator\CanonicalGenerator;
use CH\CHCore\Globals\Constants;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

(static function () {
    $extKey = Constants::EXT_KEY;

    // register `setup.typoscript`
    ExtensionManagementUtility::addTypoScript(
        $extKey,
        'setup',
        "@import 'EXT:{$extKey}/Configuration/TypoScript/setup.typoscript'"
    );

    // register `CanonicalGenerator`
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['TYPO3\CMS\Frontend\Page\PageGenerator']['generateMetaTags']['canonical'] =
        CanonicalGenerator::class . '->generate';
})();
