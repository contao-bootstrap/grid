<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

/*
 * Palettes
 */

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['gridStart'] = [
    'type'      => [
        'type',
        'bootstrap_grid',
        'bootstrap_grid_name',
    ],
    'wizard'    => ['bootstrap_grid_generateColumns',],
    'template'  => [':hide', 'customTpl'],
    'protected' => [':hide', 'protected'],
    'expert'    => [':hide', 'guests', 'cssID'],
    'invisible' => ['invisible', 'start', 'stop'],
];

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['gridSeparator'] = [
    'type'      => ['type', 'name', 'bootstrap_grid_parent'],
    'template'  => [':hide', 'customTpl'],
    'protected' => [':hide', 'protected'],
    'expert'    => [':hide', 'guests', 'cssID'],
    'invisible' => ['invisible', 'start', 'stop'],
];

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['gridStop'] = [
    'type'      => ['type', 'name', 'bootstrap_grid_parent'],
    'template'  => [':hide', 'customTpl'],
    'protected' => [':hide', 'protected'],
    'expert'    => [':hide', 'guests', 'cssID'],
    'invisible' => ['invisible', 'start', 'stop'],
];


/*
 * Fields
 */

$GLOBALS['TL_DCA']['tl_content']['fields']['bootstrap_grid'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_content']['bootstrap_grid'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => ['contao_bootstrap.grid.dca.content_data_container', 'getGridOptions'],
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

$GLOBALS['TL_DCA']['tl_content']['fields']['bootstrap_grid_name'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_content']['bootstrap_grid_name'],
    'exclude'       => true,
    'inputType'     => 'text',
    'reference'     => &$GLOBALS['TL_LANG']['tl_content'],
    'eval'          => [
        'submitOnChange'     => true,
        'includeBlankOption' => true,
        'chosen'             => true,
        'tl_class'           => 'w50'
    ],
    'save_callback' => [
        ['contao_bootstrap.grid.dca.content_data_container', 'generateGridName']
    ],
    'sql'           => "varchar(64) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['bootstrap_grid_parent'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_content']['bootstrap_grid'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => ['contao_bootstrap.grid.dca.content_data_container', 'getGridParentOptions'],
    'reference'        => &$GLOBALS['TL_LANG']['tl_content'],
    'eval'             => [
        'mandatory'          => true,
        'submitOnChange'     => true,
        'includeBlankOption' => true,
        'chosen'             => true,
        'tl_class'           => 'w50'
    ],
    'sql'              => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_content']['fields']['bootstrap_grid_generateColumns'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_content']['bootstrap_grid'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => ['contao_bootstrap.grid.dca.content_data_container', 'getGridColumns'],
    'save_callback'    => [
        ['contao_bootstrap.grid.dca.content_data_container', 'generateColumns'],
    ],
    'eval'             => [
        'includeBlankOption' => true,
        'chosen'             => true,
        'tl_class'           => 'clr w50',
        'doNotSaveEmpty'     => true,
    ],
];
