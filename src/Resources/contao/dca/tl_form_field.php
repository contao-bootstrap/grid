<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2019 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

use ContaoBootstrap\Grid\Listener\Dca\FormFieldFixParentRelationListener;

/*
 * Config
 */

$GLOBALS['TL_DCA']['tl_form_field']['config']['oncopy_callback'][] = [
    FormFieldFixParentRelationListener::class,
    'onCopy'
];

$GLOBALS['TL_DCA']['tl_form_field']['config']['onsubmit_callback'][] = [
    FormFieldFixParentRelationListener::class,
    'onSubmit'
];

/*
 * Palettes
 */

$GLOBALS['TL_DCA']['tl_form_field']['metapalettes']['bs_gridStart'] = [
    'type'           => [
        'type',
        'bs_grid',
        'bs_grid_name',
    ],
    'bs_grid_wizard' => ['bs_grid_wizard',],
    'template'       => [':hide', 'customTpl'],
    'protected'      => [':hide', 'protected'],
];

$GLOBALS['TL_DCA']['tl_form_field']['metapalettes']['bs_gridSeparator'] = [
    'type'      => ['type', 'bs_grid_parent'],
    'template'  => [':hide', 'customTpl'],
    'protected' => [':hide', 'protected'],
];

$GLOBALS['TL_DCA']['tl_form_field']['metapalettes']['bs_gridStop'] = [
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
    'options_callback' => ['contao_bootstrap.grid.listeners.dca.form', 'getGridOptions'],
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
    'foreignKey'       => 'tl_bs_grid.title'
];

$GLOBALS['TL_DCA']['tl_form_field']['fields']['bs_grid_name'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_form_field']['bs_grid_name'],
    'exclude'       => true,
    'inputType'     => 'text',
    'eval'          => [
        'includeBlankOption' => true,
        'chosen'             => true,
        'tl_class'           => 'w50'
    ],
    'save_callback' => [
        ['contao_bootstrap.grid.listeners.dca.form', 'generateGridName']
    ],
    'sql'           => "varchar(64) NOT NULL default ''",
];


$GLOBALS['TL_DCA']['tl_form_field']['fields']['bs_grid_parent'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_form_field']['bs_grid_parent'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => ['contao_bootstrap.grid.listeners.dca.form', 'getGridParentOptions'],
    'reference'        => &$GLOBALS['TL_LANG']['tl_form_field'],
    'eval'             => [
        'mandatory'          => true,
        'includeBlankOption' => true,
        'chosen'             => true,
        'doNotCopy'          => true,
        'tl_class'           => 'w50'
    ],
    'sql'              => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_form_field']['fields']['bs_grid_wizard'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_form_field']['bs_grid_wizard'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => ['contao_bootstrap.grid.listeners.dca.form', 'getGridColumns'],
    'save_callback'    => [
        ['contao_bootstrap.grid.listeners.dca.form', 'generateColumns'],
    ],
    'eval'             => [
        'includeBlankOption' => true,
        'chosen'             => true,
        'tl_class'           => 'clr w50',
        'doNotSaveEmpty'     => true,
    ],
];
