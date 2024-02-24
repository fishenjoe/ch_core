<?php

use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

(static function () {
    $schemaOrgImageCropConfiguration = [
        'config' => [
            'cropVariants' => [
                'desktop' => [
                    'disabled' => true,
                ],
                'mobile' => [
                    'disabled' => true,
                ],
                'tablet' => [
                    'disabled' => true,
                ],
                'preview' => [
                    'title' => 'Image',
                    'coverAreas' => [],
                    'cropArea' => [
                        'x' => 0.0,
                        'y' => 0.0,
                        'width' => 1.0,
                        'height' => 1.0
                    ],
                    'allowedAspectRatios' => [
                        'NaN' => [
                            'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                            'value' => 0.0
                        ],
                    ],
                    'selectedRatio' => 'NaN',
                ],
            ],
        ],
    ];

    // extend the pages' table with new fields
    $TCAColumnsForPages = [
        'ch_scroll_to_content' => [
            'exclude' => '1',
            'label' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.pages.columns.scroll2Content',
            'description' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.pages.columns.scroll2Content.descr',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'items' => [
                   [
                       0 => '',
                       1 => '',
                       'labelChecked' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:global.text.enabled',
                       'labelUnchecked' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:global.text.disabled',
                   ]
                ],
            ],
        ],
        'ch_quickrequest_disabled' => [
            'exclude' => '1',
            'label' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.pages.columns.disableQuickrequest',
            'description' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.pages.columns.disableQuickrequest.descr',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'labelChecked' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:global.text.enabled',
                        'labelUnchecked' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:global.text.disabled',
                    ]
                ],
            ],
        ],
        'ch_hide_in_mobile_menu' => [
            'exclude' => '1',
            'label' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.pages.columns.hideInMobileMenu',
            'description' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.pages.columns.hideInMobileMenu.descr',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'labelChecked' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:global.text.enabled',
                        'labelUnchecked' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:global.text.disabled',
                    ]
                ],
            ],
        ],
        'ch_heritable_content_disabled' => [
            'exclude' => '1',
            'label' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.pages.columns.disableHeritableContent',
            'description' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.pages.columns.disableHeritableContent.descr',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'labelChecked' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:global.text.enabled',
                        'labelUnchecked' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:global.text.disabled',
                    ]
                ],
            ],
        ],
        'ch_include_in_sitemap_menu' => [
            'exclude' => '1',
            'label' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.pages.columns.includeInSitemapMenu',
            'description' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:table.pages.columns.includeInSitemapMenu.descr',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'labelChecked' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:global.text.enabled',
                        'labelUnchecked' => 'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:global.text.disabled',
                    ]
                ],
            ],
        ],
        'ch_include_in_sitemap_menu_children' => [
            'exclude' => '1',
            'label' => 'LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:table.pages.columns.includeInSitemapMenuChildren',
            'description' => 'LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:table.pages.columns.includeInSitemapMenuChildren.descr',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'labelChecked' => 'LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:global.text.enabled',
                        'labelUnchecked' => 'LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:global.text.disabled',
                    ]
                ],
            ],
        ],
        'bn_custom_field' => [
            'exclude' => '1',
            'label' => 'LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:table.pages.columns.customField',
            'description' => 'LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:table.pages.columns.customField.descr',
            'config' => [
                'type' => 'input',
                'max' => 128,
                'size' => 128,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'bn_schema_org_image' => [
            'exclude' => '1',
            'label' => 'Image',
            'config' => ExtensionManagementUtility::getFileFieldTCAConfig(
                'bn_schema_org_image',
                [
                    // Use the imageoverlayPalette instead of the basicoverlayPalette
                    'overrideChildTca' => [
                        'types' => [
                            '0' => [
                                'showitem' => '
                                    --palette--;;imageoverlayPalette,
                                    --palette--;;filePalette'
                            ],
                            File::FILETYPE_IMAGE => [
                                'showitem' => '
                                    --palette--;;imageoverlayPalette,
                                    --palette--;;filePalette'
                            ]
                        ],
                        'columns' => [
                            'crop' => $schemaOrgImageCropConfiguration
                        ]
                    ],
                    'behaviour' => [
                        'allowLanguageSynchronization' => true
                    ]
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            )
        ],
        'bn_schema_org_rating_cur_value' => [
            'exclude' => '1',
            'label' => 'LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:table.pages.columns.rating.cur_value',
            'description' => 'LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:table.pages.columns.rating.cur_value.descr',
            'config' => [
                'type' => 'input',
                'max' => 128,
                'size' => 128,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'bn_schema_org_rating_max_value' => [
            'exclude' => '1',
            'label' => 'LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:table.pages.columns.rating.max_value',
            'description' => 'LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:table.pages.columns.rating.max_value.descr',
            'config' => [
                'type' => 'input',
                'max' => 128,
                'size' => 128,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'bn_schema_org_rating_amount' => [
            'exclude' => '1',
            'label' => 'LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:table.pages.columns.rating.amount',
            'description' => 'LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:table.pages.columns.rating.amount.descr',
            'config' => [
                'type' => 'input',
                'max' => 128,
                'size' => 128,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
    ];

    // add all new field definitions for pages table
    ExtensionManagementUtility::addTCAcolumns('pages', $TCAColumnsForPages);

    // add new palette for pages table
    ExtensionManagementUtility::addFieldsToPalette(
        'pages',
        'bn_miscellaneous',
        'bn_scroll_to_content,bn_quickrequest_disabled,bn_hide_in_mobile_menu,bn_heritable_content_disabled,--linebreak--,bn_custom_field'
    );
    ExtensionManagementUtility::addFieldsToPalette(
        'pages',
        'bn_seo',
        'bn_include_in_sitemap_menu,bn_include_in_sitemap_menu_children'
    );
    ExtensionManagementUtility::addFieldsToPalette(
        'pages',
        'bn_schemaorg',
        'bn_schema_org_image,--linebreak--,bn_schema_org_rating_cur_value,bn_schema_org_rating_max_value,bn_schema_org_rating_amount'
    );

    // add new tab 'settings'
    ExtensionManagementUtility::addToAllTCAtypes(
        'pages',
        '
        --div--;LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:global.tab.bnsettings,
        --palette--;LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:table.pages.palettes.misc;bn_miscellaneous,
        --palette--;LLL:EXT:bn_core/Resources/Private/Language/locallang.xlf:table.pages.palettes.seo;bn_seo,
        '
    );
    // add new palette 'schemaorg' into seo tab
    ExtensionManagementUtility::addToAllTCAtypes(
        'pages',
        '--palette--;Schema.org;bn_schemaorg',
        (string)PageRepository::DOKTYPE_DEFAULT,
        'after:canonical_link'
    );

    // add language sync to 'categories' field
    $TCAforCategories = [
        'categories' => [
            'config' => [
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
    ];
    $GLOBALS['TCA']['pages']['columns'] = array_replace_recursive($GLOBALS['TCA']['pages']['columns'], $TCAforCategories);
})();
