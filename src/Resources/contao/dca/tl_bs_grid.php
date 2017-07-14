<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */
$GLOBALS['TL_DCA']['tl_bs_grid'] = [
    // Config
    'config'       => [
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'ptable'           => 'tl_theme',
        'onload_callback'  => [
            ['contao_bootstrap.grid.dca.grid', 'initializePalette']
        ],
        'sql'              => [
            'keys' => [
                'id' => 'primary',
            ]
        ]
    ],
    // List
    'list'         => [
        'label'             => [
            'fields' => ['title'],
            'format' => '%s <span style="color:#ccc;">[%s ' . $GLOBALS['TL_LANG']['tl_bs_grid']['formatColumns'] . ']</span>',
        ],
        'sorting'           => [
            'mode'                  => 4,
            'flag'                  => 1,
            'fields'                => ['title'],
            'headerFields'          => ['name', 'author', 'tstamp'],
            'panelLayout'           => 'sort,search,limit',
            'child_record_callback' => [
                'contao_bootstrap.grid.dca.grid',
                'generateLabel'
            ],
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ]
        ],
        'operations'        => [
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_bs_grid']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif'
            ],
            'copy'   => [
                'label'      => &$GLOBALS['TL_LANG']['tl_bs_grid']['copy'],
                'href'       => 'act=copy',
                'icon'       => 'copy.gif',
                'attributes' => 'onclick="Backend.getScrollOffset()"'
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_bs_grid']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_bs_grid']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
            ]
        ],
    ],
    // Palettes
    'metapalettes' => [
        'default' => [
            'title' => ['title', 'description'],
            'grid'  => ['sizes'],
            'row'   => [':hide', 'align', 'justify', 'rowClass', 'noGutters'],
        ]
    ],
    'fields'       => [
        'id'          => [
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],
        'pid'         => [
            'foreignKey' => 'tl_theme.name',
            'relation'   => ['type' => 'hasOne', 'load' => 'lazy'],
            'sql'        => "int(10) unsigned NOT NULL default '0'"
        ],
        'tstamp'      => [
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],
        'title'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['title'],
            'exclude'   => true,
            'sorting'   => true,
            'flag'      => 1,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'description' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['description'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['tl_class' => 'clr long'],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'sizes'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['sizes'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'options'   => ['xs', 'sm', 'md', 'lg', 'xl'],
            'reference' => &$GLOBALS['TL_LANG']['tl_bs_grid'],
            'eval'      => [
                'submitOnChange' => true,
                'multiple'       => true,
                'tl_class'       => 'clr',
            ],
            'sql'       => "tinyBlob NULL"
        ],
        'xsSize'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['xsSize'],
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'eval'      => [
                'columnFields' => [
                    'width'  => [
                        'label'            => $GLOBALS['TL_LANG']['tl_bs_grid']['width'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid',
                            'getWidths'
                        ],
                        'eval'             => [
                            'style'              => 'width: 100px;',
                            'chosen'             => true,
                            'includeBlankOption' => true,
                        ],
                    ],
                    'offset' => [
                        'label'            => $GLOBALS['TL_LANG']['tl_bs_grid']['offset'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid',
                            'getOffsets'
                        ],
                        'reference'        => ['null' => '0 '],
                        'eval'             => [
                            'style'              => 'width: 100px;',
                            'includeBlankOption' => true,
                            'chosen'             => true,
                            'isAssociative'      => false
                        ],
                    ],
                    'order'  => [
                        'label'            => $GLOBALS['TL_LANG']['tl_bs_grid']['order'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid',
                            'getOrders'
                        ],
                        'eval'             => [
                            'style'              => 'width: 120px;',
                            'includeBlankOption' => true,
                            'chosen'             => true
                        ],
                    ],
                    'align'  => [
                        'label'     => $GLOBALS['TL_LANG']['tl_bs_grid']['order'],
                        'inputType' => 'select',
                        'options'   => ['start', 'center', 'end'],
                        'eval'      => [
                            'style'              => 'width: 100px;',
                            'includeBlankOption' => true,
                            'chosen'             => true
                        ],
                    ],
                    'class'  => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['class'],
                        'exclude'   => true,
                        'default'   => '',
                        'inputType' => 'text',
                        'eval'      => [
                            'style' => 'width: 160px',
                        ],
                    ],
                    'reset'  => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['class'],
                        'exclude'   => true,
                        'default'   => '',
                        'inputType' => 'checkbox',
                        'eval'      => [
                            'style' => 'width: 80px',
                        ],
                    ],
                ],
            ],
            'sql'       => "blob NULL"
        ],
        'smSize'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['smSize'],
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'eval'      => [
                'columnFields' => [
                    'width'  => [
                        'label'            => $GLOBALS['TL_LANG']['tl_bs_grid']['width'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid',
                            'getWidths'
                        ],
                        'eval'             => [
                            'style'              => 'width: 100px;',
                            'chosen'             => true,
                            'includeBlankOption' => true,
                        ],
                    ],
                    'offset' => [
                        'label'            => $GLOBALS['TL_LANG']['tl_bs_grid']['offset'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid',
                            'getOffsets'
                        ],
                        'reference'        => ['null' => '0 '],
                        'eval'             => [
                            'style'              => 'width: 100px;',
                            'includeBlankOption' => true,
                            'chosen'             => true,
                            'isAssociative'      => false
                        ],
                    ],
                    'order'  => [
                        'label'            => $GLOBALS['TL_LANG']['tl_bs_grid']['order'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid',
                            'getOrders'
                        ],
                        'eval'             => [
                            'style'              => 'width: 120px;',
                            'includeBlankOption' => true,
                            'chosen'             => true
                        ],
                    ],
                    'align'  => [
                        'label'     => $GLOBALS['TL_LANG']['tl_bs_grid']['order'],
                        'inputType' => 'select',
                        'options'   => ['start', 'center', 'end'],
                        'eval'      => [
                            'style'              => 'width: 100px;',
                            'includeBlankOption' => true,
                            'chosen'             => true
                        ],
                    ],
                    'class'  => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['class'],
                        'exclude'   => true,
                        'default'   => '',
                        'inputType' => 'text',
                        'eval'      => [
                            'style' => 'width: 160px',
                        ],
                    ],
                    'reset'  => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['class'],
                        'exclude'   => true,
                        'default'   => '',
                        'inputType' => 'checkbox',
                        'eval'      => [
                            'style' => 'width: 80px',
                        ],
                    ],
                ],
            ],
            'sql'       => "blob NULL"
        ],
        'mdSize'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['mdSize'],
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'eval'      => [
                'includeBlankOption' => true,
                'columnFields'       => [
                    'width'  => [
                        'label'            => $GLOBALS['TL_LANG']['tl_bs_grid']['width'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid',
                            'getWidths'
                        ],
                        'eval'             => [
                            'style'              => 'width: 100px;',
                            'chosen'             => true,
                            'includeBlankOption' => true,
                        ],
                    ],
                    'offset' => [
                        'label'            => $GLOBALS['TL_LANG']['tl_bs_grid']['offset'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid',
                            'getOffsets'
                        ],
                        'reference'        => ['null' => '0 '],
                        'eval'             => [
                            'style'              => 'width: 100px;',
                            'includeBlankOption' => true,
                            'chosen'             => true,
                            'isAssociative'      => false
                        ],
                    ],
                    'order'  => [
                        'label'            => $GLOBALS['TL_LANG']['tl_bs_grid']['order'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid',
                            'getOrders'
                        ],
                        'eval'             => [
                            'style'              => 'width: 120px;',
                            'includeBlankOption' => true,
                            'chosen'             => true
                        ],
                    ],
                    'align'  => [
                        'label'     => $GLOBALS['TL_LANG']['tl_bs_grid']['order'],
                        'inputType' => 'select',
                        'options'   => ['start', 'center', 'end'],
                        'eval'      => [
                            'style'              => 'width: 100px;',
                            'includeBlankOption' => true,
                            'chosen'             => true
                        ],
                    ],
                    'class'  => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['class'],
                        'exclude'   => true,
                        'default'   => '',
                        'inputType' => 'text',
                        'eval'      => [
                            'style' => 'width: 160px',
                        ],
                    ],
                    'reset'  => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['class'],
                        'exclude'   => true,
                        'default'   => '',
                        'inputType' => 'checkbox',
                        'eval'      => [
                            'style' => 'width: 80px',
                        ],
                    ],
                ],
            ],
            'sql'       => "blob NULL"
        ],
        'lgSize'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['lgSize'],
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'eval'      => [
                'includeBlankOption' => true,
                'columnFields'       => [
                    'width'  => [
                        'label'            => $GLOBALS['TL_LANG']['tl_bs_grid']['width'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid',
                            'getWidths'
                        ],
                        'eval'             => [
                            'style'              => 'width: 100px;',
                            'chosen'             => true,
                            'includeBlankOption' => true,
                        ],
                    ],
                    'offset' => [
                        'label'            => $GLOBALS['TL_LANG']['tl_bs_grid']['offset'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid',
                            'getOffsets'
                        ],
                        'reference'        => ['null' => '0 '],
                        'eval'             => [
                            'style'              => 'width: 100px;',
                            'includeBlankOption' => true,
                            'chosen'             => true,
                            'isAssociative'      => false
                        ],
                    ],
                    'order'  => [
                        'label'            => $GLOBALS['TL_LANG']['tl_bs_grid']['order'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid',
                            'getOrders'
                        ],
                        'eval'             => [
                            'style'              => 'width: 120px;',
                            'includeBlankOption' => true,
                            'chosen'             => true
                        ],
                    ],
                    'align'  => [
                        'label'     => $GLOBALS['TL_LANG']['tl_bs_grid']['order'],
                        'inputType' => 'select',
                        'options'   => ['start', 'center', 'end'],
                        'eval'      => [
                            'style'              => 'width: 100px;',
                            'includeBlankOption' => true,
                            'chosen'             => true
                        ],
                    ],
                    'class'  => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['class'],
                        'exclude'   => true,
                        'default'   => '',
                        'inputType' => 'text',
                        'eval'      => [
                            'style' => 'width: 160px',
                        ],
                    ],
                    'reset'  => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['class'],
                        'exclude'   => true,
                        'default'   => '',
                        'inputType' => 'checkbox',
                        'eval'      => [
                            'style' => 'width: 80px',
                        ],
                    ],
                ],
            ],
            'sql'       => "blob NULL"
        ],
        'xlSize'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['xlSize'],
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'eval'      => [
                'includeBlankOption' => true,
                'columnFields'       => [
                    'width'  => [
                        'label'            => $GLOBALS['TL_LANG']['tl_bs_grid']['width'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid',
                            'getWidths'
                        ],
                        'eval'             => [
                            'style'              => 'width: 100px;',
                            'chosen'             => true,
                            'includeBlankOption' => true,
                        ],
                    ],
                    'offset' => [
                        'label'            => $GLOBALS['TL_LANG']['tl_bs_grid']['offset'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid',
                            'getOffsets'
                        ],
                        'reference'        => ['null' => '0 '],
                        'eval'             => [
                            'style'              => 'width: 100px;',
                            'includeBlankOption' => true,
                            'chosen'             => true,
                            'isAssociative'      => false
                        ],
                    ],
                    'order'  => [
                        'label'            => $GLOBALS['TL_LANG']['tl_bs_grid']['order'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid',
                            'getOrders'
                        ],
                        'eval'             => [
                            'style'              => 'width: 120px;',
                            'includeBlankOption' => true,
                            'chosen'             => true
                        ],
                    ],
                    'align'  => [
                        'label'     => $GLOBALS['TL_LANG']['tl_bs_grid']['order'],
                        'inputType' => 'select',
                        'options'   => ['start', 'center', 'end'],
                        'eval'      => [
                            'style'              => 'width: 100px;',
                            'includeBlankOption' => true,
                            'chosen'             => true
                        ],
                    ],
                    'class'  => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['class'],
                        'exclude'   => true,
                        'default'   => '',
                        'inputType' => 'text',
                        'eval'      => [
                            'style' => 'width: 160px',
                        ],
                    ],
                    'reset'  => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['class'],
                        'exclude'   => true,
                        'default'   => '',
                        'inputType' => 'checkbox',
                        'eval'      => [
                            'style' => 'width: 80px',
                        ],
                    ],
                ],
            ],
            'sql'       => "blob NULL"
        ],
        'align'       => [
            'label'     => $GLOBALS['TL_LANG']['tl_bs_grid']['align'],
            'inputType' => 'select',
            'options'   => ['start', 'center', 'end'],
            'eval'      => [
                'includeBlankOption' => true,
                'chosen'             => true,
                'tl_class'           => 'w50',
            ],
            'sql'       => 'varchar(64) NOT NULL default \'\''
        ],
        'justify'     => [
            'label'     => $GLOBALS['TL_LANG']['tl_bs_grid']['justify'],
            'inputType' => 'select',
            'options'   => ['start', 'center', 'end', 'around', 'between'],
            'eval'      => [
                'includeBlankOption' => true,
                'chosen'             => true,
                'tl_class'           => 'w50',
            ],
            'sql'       => 'varchar(64) NOT NULL default \'\''
        ],
        'rowClass'    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['rowClass'],
            'exclude'   => true,
            'default'   => '',
            'inputType' => 'text',
            'eval'      => [
                'tl_class' => 'clr w50'
            ],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'noGutters'   => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_bs_grid']['noGutters'],
            'exclude'   => true,
            'default'   => '',
            'inputType' => 'checkbox',
            'reference' => &$GLOBALS['TL_LANG']['tl_bs_grid'],
            'eval'      => array(
                'tl_class' => 'w50 m12',
            ),
            'sql'       => "char(1) NULL"
        )
    ]
];
