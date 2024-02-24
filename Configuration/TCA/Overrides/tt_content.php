<?php

use CH\CHCore\Services\TcaService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

(static function () {

    // Adds new Divider to the "Type" dropdown
    ExtensionManagementUtility::addTcaSelectItem(
        'tt_content',
        'CType',
        ['Brandnamic Elements', '--div--'],
        'form_formframework',
        'after'
    );

    // Adds the content element to the "Type" dropdown
    ExtensionManagementUtility::addPlugin(
        [
            'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:vista.switch.label',
            'ch_vista_switch',
            'EXT:ch_core/Resources/Public/Images/ch_45x45.png'
        ],
        'CType',
        'ch_core'
    );
    ExtensionManagementUtility::addPlugin(
        [
            'LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:vista.element.label',
            'ch_vista_element',
            'EXT:ch_core/Resources/Public/Images/ch_45x45.png'
        ],
        'CType',
        'ch_core'
    );

    $TCAforVistaSwitch = [
        'types' => [
            'ch_vista_switch' => [
                'showitem' => '
                    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
                    selected_categories;LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:vista.switch.categories_selection,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    --palette--;;hidden,
                    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended
                ',
                'columnsOverrides' => [
                    'selected_categories' => [
                        'config' => [
                            'size' => 7,
                            'treeConfig' => [
                                'rootUid' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];
    $TCAforVistaElement = [
        'types' => [
            'ch_vista_element' => [
                'showitem' => '
                    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
                    selected_categories;LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:vista.element.category_selection,
                    assets;LLL:EXT:ch_core/Resources/Private/Language/locallang.xlf:vista.element.items,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    --palette--;;hidden,
                    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended
                ',
                'columnsOverrides' => [
                    'selected_categories' => [
                        'config' => [
                            'size' => 1,
                            'type' => 'select',
                            'renderType' => 'selectSingle',
                            'foreign_table_where' => 'AND {#sys_category}.{#sys_language_uid} IN (-1, 0) AND {#sys_category}.{#parent} = 1',
                        ],
                    ],
                    'assets' => [
                        'config' => ExtensionManagementUtility::getFileFieldTCAConfig(
                            'assets',
                            [
                                'overrideChildTca' => [
                                    'columns' => [
                                        'uid_local' => [
                                            'config' => [
                                                'appearance' => [
                                                    'elementBrowserAllowed' => 'gif,jpg,jpeg,bmp,png,pdf,svg,ai,mp4,webm,youtube,vimeo'
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            $GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext'],
                            'mp3,wav,ogg,flac,opus'
                        )
                    ],
                ],
            ],
        ],
    ];
    $TCAforGeneralPurpose = [
        'ctrl' => [
            'label_userFunc' => TcaService::class . '->getTtContentTitle'
        ],
    ];

    // add the config to the global TCA array
    $GLOBALS['TCA']['tt_content'] = array_replace_recursive($GLOBALS['TCA']['tt_content'], $TCAforVistaSwitch);
    $GLOBALS['TCA']['tt_content'] = array_replace_recursive($GLOBALS['TCA']['tt_content'], $TCAforVistaElement);
    $GLOBALS['TCA']['tt_content'] = array_replace_recursive($GLOBALS['TCA']['tt_content'], $TCAforGeneralPurpose);
})();
