<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2020 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0-or-later
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = [
    'contao_bootstrap.grid.listeners.dca.module',
    'initialize'
];

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
    'options_callback' => ['contao_bootstrap.grid.listeners.dca.module', 'getGridOptions'],
    'reference'        => &$GLOBALS['TL_LANG']['tl_content'],
    'eval'             => [
        'mandatory'          => false,
        'includeBlankOption' => true,
        'chosen'             => true,
        'tl_class'           => 'w50',
    ],
    'load_callback'  => [
        ['contao_bootstrap.grid.listeners.dca.module', 'setGridWidgetOptions']
    ],
    'sql'              => "int(10) unsigned NOT NULL default '0'",
    'relation'         => ['type' => 'hasOne', 'load' => 'lazy'],
    'foreignKey'       => 'tl_bs_grid.title',
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
                'options_callback' => ['contao_bootstrap.grid.listeners.dca.module', 'getAllModules'],
                'reference'        => $GLOBALS['TL_LANG']['tl_module']['bs_gridModule'],
                'eval'             => [
                    'style'              => 'width: 500px',
                    'includeBlankOption' => true,
                    'chosen'             => true,
                ],
            ],
            'inactive' => [
                'label'     => &$GLOBALS['TL_LANG']['tl_module']['bs_gridModules_inactive'],
                'inputType' => 'checkbox',
                'eval'      => ['style' => 'width: 20px'],
            ],
        ],
    ],
    'sql'       => 'blob NULL',
];
