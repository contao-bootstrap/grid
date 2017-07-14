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

$GLOBALS['TL_DCA']['tl_form_field']['metapalettes']['gridStart'] = [
    'type'           => [
        'type',
        'bs_grid',
        'bs_grid_name',
    ],
    'bs_grid_wizard' => ['bs_grid_generateColumns',],
    'template'       => [':hide', 'customTpl'],
    'protected'      => [':hide', 'protected'],
];

$GLOBALS['TL_DCA']['tl_form_field']['metapalettes']['gridSeparator'] = [
    'type'      => ['type', 'bs_grid_parent'],
    'template'  => [':hide', 'customTpl'],
    'protected' => [':hide', 'protected'],
];

$GLOBALS['TL_DCA']['tl_form_field']['metapalettes']['gridStop'] = [
    'type'      => ['type', 'bs_grid_parent'],
    'template'  => [':hide', 'customTpl'],
    'protected' => [':hide', 'protected'],
];


/*
 * Fields
 */

$GLOBALS['TL_DCA']['tl_form_field']['fields']['bs_grid'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_form_field']['bs_grid'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => ['contao_bootstrap.grid.dca.form', 'getGridOptions'],
    'reference'        => &$GLOBALS['TL_LANG']['tl_form_field'],
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

$GLOBALS['TL_DCA']['tl_form_field']['fields']['bs_grid_name'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_form_field']['bs_grid_name'],
    'exclude'       => true,
    'inputType'     => 'text',
    'eval'          => [
        'submitOnChange'     => true,
        'includeBlankOption' => true,
        'chosen'             => true,
        'tl_class'           => 'w50'
    ],
    'save_callback' => [
        ['contao_bootstrap.grid.dca.form', 'generateGridName']
    ],
    'sql'           => "varchar(64) NOT NULL default ''",
];


$GLOBALS['TL_DCA']['tl_form_field']['fields']['bs_grid_parent'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_form_field']['bs_grid'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => ['contao_bootstrap.grid.dca.form', 'getGridParentOptions'],
    'reference'        => &$GLOBALS['TL_LANG']['tl_form_field'],
    'eval'             => [
        'mandatory'          => true,
        'submitOnChange'     => true,
        'includeBlankOption' => true,
        'chosen'             => true,
        'tl_class'           => 'w50'
    ],
    'sql'              => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_form_field']['fields']['bs_grid_generateColumns'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_form_field']['bs_grid'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => ['contao_bootstrap.grid.dca.form', 'getGridColumns'],
    'save_callback'    => [
        ['contao_bootstrap.grid.dca.form', 'generateColumns'],
    ],
    'eval'             => [
        'includeBlankOption' => true,
        'chosen'             => true,
        'tl_class'           => 'clr w50',
        'doNotSaveEmpty'     => true,
    ],
];
