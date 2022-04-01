<?php

declare(strict_types=1);

// Palette
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DataContainer;
use Contao\StringUtil;

PaletteManipulator::create()
    ->addLegend('bootstrap_legend', '')
    ->addField('bs_grid_columns', 'bootstrap')
    ->addField('bs_grid_sizes', 'bootstrap')
    ->addField('bs_grid_default_size', 'bootstrap')
    ->applyToPalette('default', 'tl_theme');

// Operations
array_insert(
    $GLOBALS['TL_DCA']['tl_theme']['list']['operations'],
    -1,
    [
        'bs_grid' => [
            'href'  => 'table=tl_bs_grid',
            'label' => &$GLOBALS['TL_LANG']['tl_theme']['bs_grid'],
            'icon'  => 'bundles/contaobootstrapgrid/img/icon.png',
        ],
    ]
);

// Fields
$GLOBALS['TL_DCA']['tl_theme']['fields']['bs_grid_columns'] = [
    'label'             => &$GLOBALS['TL_LANG']['tl_theme']['bs_grid_columns'],
    'inputType'         => 'text',
    'eval'              => ['tl_class' => 'w50'],
    'sql'               => 'int(10) NULL default NULL',
];

$GLOBALS['TL_DCA']['tl_theme']['fields']['bs_grid_sizes'] = [
    'label'             => &$GLOBALS['TL_LANG']['tl_theme']['bs_grid_sizes'],
    'inputType'         => 'listWizard',
    'default'           => [
        'xs',
        'sm',
        'md',
        'lg',
        'xl',
    ],
    'save_callback' => [
        // @codingStandardsIgnoreStart - PHPCS doesnt understand static keyword
        static function ($value) {
            return array_values(
                array_unique(
                    array_filter(StringUtil::deserialize($value, true))
                )
            );
        }
        // @codingStandardsIgnoreEnd
    ],
    'eval'              => [
        'tl_class' => 'clr w50',
        'rgxp'     => 'fieldname',
    ],
    'sql'               => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_theme']['fields']['bs_grid_default_size'] = [
    'label'             => &$GLOBALS['TL_LANG']['tl_theme']['bs_grid_default_size'],
    'inputType'         => 'select',
    'default'           => 'xs',
    'options_callback'  => static function (DataContainer $dataContainer) {
        return StringUtil::deserialize($dataContainer->activeRecord->bs_grid_sizes, true);
    },
    'eval'              => ['tl_class' => 'w50'],
    'sql'               => 'varchar(32) NOT NULL default \'\'',
];
