<?php

use CH\CHCore\Globals\Constants;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

$extKey = Constants::EXT_KEY;

return [
    'tx-chcore-default' => [
        'provider' => SvgIconProvider::class,
        'source' => "EXT:{$extKey}/Resources/Public/Icons/Extension.svg"
    ],
];
