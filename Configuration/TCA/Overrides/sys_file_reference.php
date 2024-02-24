<?php

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

(static function () {

    // preset for the aspect ratios
    $allowedAspectRatiosPreset = [
        'NaN' => [
            'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
            'value' => 0.0
        ],
        '1:1' => [
            'title' => '1:1',
            'value' => 1
        ],
        '4:3' => [
            'title' => '4:3',
            'value' => 4/3
        ],
        '4:5' => [
            'title' => '4:5',
            'value' => 4/5
        ],
        '16:9' => [
            'title' => '16:9',
            'value' => 16/9
        ],
        '21:9' => [
            'title' => '21:9',
            'value' => 21/9
        ],
    ];

    // extend the sys_file_reference table
    $TCAForSysFileReference = [
        'crop' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.crop',
            'config' => [
                'type' => 'imageManipulation',
                'cropVariants' => [
                    'desktop' => [
                        'title' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.crop.variants.desktop',
                        'selectedRatio' => 'NaN',
                        'allowedAspectRatios' => $allowedAspectRatiosPreset,
                    ],
                    'tablet' => [
                        'title' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.crop.variants.tablet',
                        'selectedRatio' => 'NaN',
                        'allowedAspectRatios' => $allowedAspectRatiosPreset,
                    ],
                    'mobile' => [
                        'title' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.crop.variants.mobile',
                        'selectedRatio' => 'NaN',
                        'allowedAspectRatios' => $allowedAspectRatiosPreset,
                    ],
                ],
            ],
        ],
        'ch_video_attributes' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.ch_video_attributes',
            'description' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.ch_video_attributes.descr',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectCheckBox',
                'default' => 'autoplay,loop,muted,playsinline',
                'items' => [
                    [
                        'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.ch_video_attributes.autoplay',
                        'autoplay',
                        '',
                        null,
                        'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.ch_video_attributes.autoplay.desc',
                    ],
                    [
                        'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.ch_video_attributes.loop',
                        'loop',
                        '',
                        null,
                        'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.ch_video_attributes.loop.desc',
                    ],
                    [
                        'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.ch_video_attributes.muted',
                        'muted',
                        '',
                        null,
                        'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.ch_video_attributes.muted.desc',
                    ],
                    [
                        'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.ch_video_attributes.controls',
                        'controls',
                        '',
                        null,
                        'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.ch_video_attributes.controls.desc',
                    ],
                    [
                        'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.ch_video_attributes.playsinline',
                        'playsinline',
                        '',
                        null,
                        'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.ch_video_attributes.playsinline.desc',
                    ],
                ],
            ],
        ],
        'ch_video_fallback' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.ch_video_fallback',
            'description' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.bn_video_fallback.desc',
            'config' => ExtensionManagementUtility::getFileFieldTCAConfig('bn_video_fallback', [
                'maxitems' => 1,
                'overrideChildTca' => [
                    'types' => [
                        File::FILETYPE_VIDEO => [
                            'showitem' => '
                                --palette--;;videoFallbackOverlayPalette,
                                --palette--;;filePalette'
                        ],
                    ],
                    'columns' => [
                        'uid_local' => [
                            'config' => [
                                'appearance' => [
                                    'elementBrowserAllowed' => 'mp4,ogg,webm,gif'
                                ],
                            ],
                        ],
                    ],
                ],
            ], 'mp4,ogg,webm,gif')
        ],
        'bn_video_sources' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.bn_video_sources',
            'description' => 'LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.bn_video_sources.desc',
            'config' => ExtensionManagementUtility::getFileFieldTCAConfig('bn_video_sources', [
                'maxitems' => 2,
                'overrideChildTca' => [
                    'types' => [
                        File::FILETYPE_VIDEO => [
                            'showitem' => '
                                --palette--;;videoSourceOverlayPalette,
                                --palette--;;filePalette'
                        ],
                    ],
                    'columns' => [
                        'uid_local' => [
                            'config' => [
                                'appearance' => [
                                    'elementBrowserAllowed' => 'mp4,ogg,webm,gif'
                                ],
                            ],
                        ],
                    ],
                ],
            ], 'mp4,ogg,webm,gif')
        ],
        'bn_video_sources_resolution' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.bn_video_sources_resolution',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.bn_video_sources_resolution.tablet',
                        'tablet'
                    ],
                    [
                        'LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:table.sys_file_reference.bn_video_sources_resolution.phone',
                        'phone'
                    ],
                ],
            ],
        ],

    ];

    // add new TCA columns to existing definition
    ExtensionManagementUtility::addTCAcolumns('sys_file_reference', $TCAForSysFileReference);

    // create new palettes
    $TCAPaletteForSysFileReference = [
        'palettes' => [
            'videoOverlayPalette' => [
                'showitem' => 'title,description,--linebreak--,bn_video_attributes,--linebreak--,bn_video_sources,--linebreak--,bn_video_fallback',
            ],
            'videoFallbackOverlayPalette' => [
                'showitem' => 'bn_video_sources,--linebreak--,bn_video_fallback',
            ],
            'videoSourceOverlayPalette' => [
                'showitem' => 'bn_video_sources_resolution',
            ],
        ]
    ];

    // add new palette definitions to existing definition
    $GLOBALS['TCA']['sys_file_reference'] = array_replace_recursive(
        $GLOBALS['TCA']['sys_file_reference'],
        $TCAPaletteForSysFileReference
    );
})();
