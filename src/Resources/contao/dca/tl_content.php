<?php

/**
 * @package    Website
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['gridStart'] = [
    'type' => ['type', 'name', 'bootstrap_grid'],
    'template' => [':hide', 'customTpl'],
    'protected' => [':hide', 'protected'],
    'expert' => [':hide', 'guests', 'cssID'],
    'invisible' => ['invisible', 'start', 'stop'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['bootstrap_grid'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['bootstrap_grid'],
    'exclude'                 => true,
    'inputType'               => 'select',
    'options_callback'        => ['contao_bootstrap.grid.dca.content_data_container', 'getGridOptions'],
    'reference'               => &$GLOBALS['TL_LANG']['tl_content'],
    'eval'                    => [
        'mandatory' => true,
        'submitOnChange' => true,
        'tl_class' => 'w50'
    ],
    'sql'                     => "int(10) unsigned NOT NULL default '0'"
];
