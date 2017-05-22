<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */
$GLOBALS['TL_DCA']['tl_grid'] = [
    // Config
    'config' => [
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'ptable'           => 'tl_theme',
        'sql'              => [
            'keys' => [
                'id' => 'primary',
            ]
        ]
    ],
    'fields' => [
        'id'          => [
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],
        'pid'         => [
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],
        'tstamp'      => [
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],
        'title'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_grid']['title'],
            'exclude'   => true,
            'sorting'   => true,
            'flag'      => 1,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'description' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_grid']['description'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'xsColumns'   => [
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'eval'      => [
                'includeBlankOption' => true,
                'columnFields'       => [
                    'width'  => [
                        'label'            => $GLOBALS['TL_LANG']['tl_grid']['width'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid_data_container',
                            'getWidths'
                        ],
                        'eval'             => ['style' => 'width: 100px;', 'chosen' => true],
                    ],
                    'offset' => [
                        'label'            => $GLOBALS['TL_LANG']['tl_grid']['offset'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid_data_container',
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
                        'label'            => $GLOBALS['TL_LANG']['tl_grid']['order'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid_data_container',
                            'getOrders'
                        ],
                        'eval'             => [
                            'style'              => 'width: 160px;',
                            'includeBlankOption' => true,
                            'chosen'             => true
                        ],
                    ],
                    'align'  => [
                        'label'            => $GLOBALS['TL_LANG']['tl_grid']['order'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.dca.grid_data_container',
                            'getOrders'
                        ],
                        'eval'             => [
                            'style'              => 'width: 160px;',
                            'includeBlankOption' => true,
                            'chosen'             => true
                        ],
                    ],
                ],
                'buttons'            => ['copy' => false, 'delete' => false],
            ],
            'sql'       => "blob NULL"
        ],
        'rowClass'    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_grid']['rowClass'],
            'exclude'   => true,
            'default'   => '',
            'inputType' => 'text',
            'reference' => &$GLOBALS['TL_LANG']['tl_grid'],
            'eval'      => [],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
    ]
];
