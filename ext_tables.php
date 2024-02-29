<?php

defined('TYPO3') or die();

use CH\CHCore\Globals\Constants;

(static function () {
    $extKey = Constants::EXT_KEY;

    // register the rte presets
    if (empty($GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['ch_default'])) {
        $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['ch_default'] = "EXT:{$extKey}/Configuration/RTE/ch_default.yaml";
    }
    if (empty($GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['ch_editor'])) {
        $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['ch_editor'] = "EXT:{$extKey}/Configuration/RTE/ch_editor.yaml";
    }
})();
