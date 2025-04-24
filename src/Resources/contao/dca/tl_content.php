<?php

declare(strict_types=1);

use ContaoBootstrap\Grid\Listener\Dca\ContentFixParentRelationListener;

/*
 * Config
 */

$GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] = [
    'contao_bootstrap.grid.listeners.dca.content',
    'initializeDca',
];

$GLOBALS['TL_DCA']['tl_content']['config']['oncopy_callback'][] = [
    ContentFixParentRelationListener::class,
    'onCopy',
];

$GLOBALS['TL_DCA']['tl_content']['config']['onsubmit_callback'][] = [
    ContentFixParentRelationListener::class,
    'onSubmit',
];

/*
 * Palettes
 */

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['bs_gridStart'] = [
    'type'           => [
        'type',
        'bs_grid',
        'bs_grid_name',
    ],
    'bs_grid_wizard' => ['bs_grid_wizard'],
    'template'       => [':hide', 'customTpl'],
    'protected'      => [':hide', 'protected'],
    'expert'         => [':hide', 'guests', 'cssID'],
    'invisible'      => ['invisible', 'start', 'stop'],
];

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['bs_gridSeparator'] = [
    'type'      => ['type', 'name', 'bs_grid_parent'],
    'template'  => [':hide', 'customTpl'],
    'protected' => [':hide', 'protected'],
    'expert'    => [':hide', 'guests', 'cssID'],
    'invisible' => ['invisible', 'start', 'stop'],
];

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['bs_gridStop'] = [
    'type'      => ['type', 'name', 'bs_grid_parent'],
    'template'  => [':hide', 'customTpl'],
    'protected' => [':hide', 'protected'],
    'expert'    => [':hide', 'guests'],
    'invisible' => ['invisible', 'start', 'stop'],
];

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['bs_grid_gallery'] = [
    'type'      => ['type', 'headline'],
    'source'    => ['multiSRC', 'sortBy', 'metaIgnore'],
    'image'     => ['bs_grid', 'fullsize', 'bs_image_sizes', 'perPage', 'numberOfItems'],
    'template'  => [':hide', 'galleryTpl', 'customTpl'],
    'protected' => [':hide', 'protected'],
    'expert'    => [':hide', 'guests', 'cssID', 'useHomeDir'],
    'invisible' => ['invisible', 'start', 'stop'],
];

/*
 * Fields
 */

$GLOBALS['TL_DCA']['tl_content']['fields']['bs_grid'] = [
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => ['contao_bootstrap.grid.listeners.dca.content', 'getGridOptions'],
    'reference'        => &$GLOBALS['TL_LANG']['tl_content'],
    'eval'             => [
        'mandatory'          => true,
        'includeBlankOption' => true,
        'chosen'             => true,
        'tl_class'           => 'w50',
    ],
    'sql'              => "int(10) unsigned NOT NULL default '0'",
    'relation'         => ['type' => 'hasOne', 'load' => 'lazy'],
    'foreignKey'       => 'tl_bs_grid.title',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['bs_grid_name'] = [
    'exclude'       => true,
    'inputType'     => 'text',
    'reference'     => &$GLOBALS['TL_LANG']['tl_content'],
    'eval'          => [
        'includeBlankOption' => true,
        'chosen'             => true,
        'tl_class'           => 'w50',
    ],
    'save_callback' => [
        ['contao_bootstrap.grid.listeners.dca.content', 'generateGridName'],
    ],
    'sql'           => "varchar(64) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['bs_grid_parent'] = [
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => ['contao_bootstrap.grid.listeners.dca.content', 'getGridParentOptions'],
    'reference'        => &$GLOBALS['TL_LANG']['tl_content'],
    'eval'             => [
        'mandatory'          => true,
        'includeBlankOption' => true,
        'chosen'             => true,
        'doNotCopy'          => true,
        'tl_class'           => 'w50',
    ],
    'sql'              => "int(10) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['bs_grid_wizard'] = [
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => ['contao_bootstrap.grid.listeners.dca.content', 'getGridColumns'],
    'save_callback'    => [
        ['contao_bootstrap.grid.listeners.dca.content', 'generateColumns'],
    ],
    'eval'             => [
        'includeBlankOption' => true,
        'chosen'             => true,
        'tl_class'           => 'clr w50',
        'doNotSaveEmpty'     => true,
    ],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['bs_image_sizes'] = [
    'exclude'   => true,
    'inputType' => 'group',
    'palette'   => ['size', 'width', 'height'],
    'fields'    => [
        'size'   => [
            'label'            => &$GLOBALS['TL_LANG']['MSC']['imgSize'],
            'exclude'          => true,
            'inputType'        => 'select',
            'reference'        => &$GLOBALS['TL_LANG']['MSC'],
            'eval'             => [
                'includeBlankOption' => true,
                'nospace'            => true,
                'tl_class'           => 'w50 w33',
            ],
            'options_callback' => ['contao_bootstrap.grid.listeners.dca.content', 'getImageSizes'],
        ],
        'width'  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['bs_image_sizes__width'],
            'exclude'   => true,
            'inputType' => 'text',
            'reference' => &$GLOBALS['TL_LANG']['tl_content'],
            'eval'      => [
                'includeBlankOption' => true,
                'chosen'             => true,
                'class'              => 'tl_imageSize_0',
                'tl_class'           => 'w50 w33',
            ],
        ],
        'height' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['bs_image_sizes__height'],
            'exclude'   => true,
            'inputType' => 'text',
            'reference' => &$GLOBALS['TL_LANG']['tl_content'],
            'eval'      => [
                'includeBlankOption' => true,
                'chosen'             => true,
                'class'              => 'tl_imageSize_1',
                'tl_class'           => 'w50 w33',
            ],
        ],
    ],
    'eval'      => ['tl_class' => 'clr lng bs-image-sizes'],
    'sql'       => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['multiSRC']['load_callback'][] = [
    'contao_bootstrap.grid.listeners.dca.content',
    'setMultiSrcFlags',
];
