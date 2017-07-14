<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

$GLOBALS['TL_DCA']['tl_module']['metapalettes']['bs_grid'] = [
    'title'     => ['name', 'headline', 'type'],
    'config'    => ['bs_grid', 'bs_gridModules'],
    'template'  => [':hide', 'customTpl'],
    'protected' => [':hide', 'protected'],
    'expert'    => [':hide', 'guests', 'cssID'],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['bs_grid'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_module']['bs_grid'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => ['contao_bootstrap.grid.dca.module', 'getGridOptions'],
    'reference'        => &$GLOBALS['TL_LANG']['tl_content'],
    'eval'             => [
        'mandatory'          => true,
        'submitOnChange'     => true,
        'includeBlankOption' => true,
        'chosen'             => true,
        'tl_class'           => 'w50'
    ],
    'sql'              => "int(10) unsigned NOT NULL default '0'",
    'relation'         => ['type' => 'hasOne', 'load' => 'lazy'],
    'foreignKey'       => 'tl_grid.title'
];

$GLOBALS['TL_DCA']['tl_module']['fields']['bs_gridModules'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['bs_gridModules'],
    'exclude'   => true,
    'inputType' => 'multiColumnWizard',
    'eval'      => [
        'tl_class'     => 'clr',
        'columnFields' => [
            'module'   => [
                'label'            => &$GLOBALS['TL_LANG']['tl_module']['bs_gridModule'],
                'inputType'        => 'select',
                'options_callback' => ['contao_bootstrap.grid.dca.module', 'getAllModules'],
                'reference'        => $GLOBALS['TL_LANG']['tl_module']['bs_gridModule'],
                'eval'             => [
                    'style'              => 'width: 500px',
                    'includeBlankOption' => true,
                    'chosen'             => true
                ],
            ],
            'inactive' => [
                'label'     => &$GLOBALS['TL_LANG']['tl_module']['bs_gridModules_inactive'],
                'inputType' => 'checkbox',
                'eval'      => ['style' => 'width: 20px'],
            ],
        ]
    ],
    'sql'       => "blob NULL"
];
