<?php

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = [
    'contao_bootstrap.grid.listeners.dca.module',
    'initialize',
];

$GLOBALS['TL_DCA']['tl_module']['metapalettes']['bs_grid'] = [
    'title'     => ['name', 'headline', 'type'],
    'config'    => ['bs_grid', 'bs_gridModules'],
    'template'  => [':hide', 'customTpl'],
    'protected' => [':hide', 'protected'],
    'expert'    => [':hide', 'guests', 'cssID'],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['bs_grid'] = [
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
    'load_callback'    => [
        ['contao_bootstrap.grid.listeners.dca.module', 'setGridWidgetOptions'],
    ],
    'sql'              => "int(10) unsigned NOT NULL default '0'",
    'relation'         => ['type' => 'hasOne', 'load' => 'lazy'],
    'foreignKey'       => 'tl_bs_grid.title',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['bs_gridModules'] = [
    'exclude'   => true,
    'inputType' => 'group',
    'palette'   => ['module', 'inactive'],
    'fields'    => [
        'module'   => [
            'label'            => &$GLOBALS['TL_LANG']['tl_module']['bs_gridModule'],
            'inputType'        => 'select',
            'options_callback' => ['contao_bootstrap.grid.listeners.dca.module', 'getAllModules'],
            'reference'        => $GLOBALS['TL_LANG']['tl_module']['bs_gridModule'],
            'eval'             => [
                'includeBlankOption' => true,
                'chosen'             => true,
                'tl_class'           => 'w50',
            ],
        ],
        'inactive' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_module']['bs_gridModules_inactive'],
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50 m12'],
        ],
    ],
    'eval'      => ['tl_class' => 'clr'],
    'sql'       => 'blob NULL',
];
