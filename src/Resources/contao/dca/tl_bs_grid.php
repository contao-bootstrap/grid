<?php

declare(strict_types=1);

use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_bs_grid'] = [
    // Config
    'config'       => [
        'dataContainer'    => DC_Table::class,
        'enableVersioning' => true,
        'ptable'           => 'tl_theme',
        'onload_callback'  => [
            ['contao_bootstrap.grid.listeners.dca.grid', 'enterContext'],
            ['contao_bootstrap.grid.listeners.dca.grid', 'initializePalette'],
        ],
        'sql'              => [
            'keys' => ['id' => 'primary'],
        ],
    ],
    // List configuration
    'list'         => [
        'label'             => [
            'fields' => ['title'],
            'format' => '%s <span style="color:#ccc;">[%s '
                . ($GLOBALS['TL_LANG']['tl_bs_grid']['formatColumns'] ?? '')
                . ']</span>',
        ],
        'sorting'           => [
            'mode'                  => 4,
            'flag'                  => 1,
            'fields'                => ['title'],
            'headerFields'          => ['name', 'author', 'tstamp'],
            'panelLayout'           => 'sort,search,limit',
            'child_record_callback' => [
                'contao_bootstrap.grid.listeners.dca.grid',
                'generateLabel',
            ],
        ],
        'global_operations' => [
            'all' => [
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations'        => [
            'edit'   => [
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'copy'   => [
                'href'       => 'act=copy',
                'icon'       => 'copy.gif',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
            ],
            'delete' => [
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null)
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'show'   => [
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],
    // Palettes
    'metapalettes' => [
        'default' => [
            'title' => ['title', 'description'],
            'grid'  => ['sizes'],
            'row'   => [':hide', 'align', 'justify', 'rowClass', 'noGutters'],
        ],
    ],
    'fields'       => [
        'id'          => ['sql' => 'int(10) unsigned NOT NULL auto_increment'],
        'pid'         => [
            'foreignKey' => 'tl_theme.name',
            'relation'   => ['type' => 'hasOne', 'load' => 'lazy'],
            'sql'        => 'int(10) unsigned NOT NULL default \'0\'',
        ],
        'tstamp'      => ['sql' => 'int(10) unsigned NOT NULL default \'0\''],
        'title'       => [
            'exclude'   => true,
            'sorting'   => true,
            'flag'      => 1,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => 'varchar(255) NOT NULL default \'\'',
        ],
        'description' => [
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['tl_class' => 'clr long'],
            'sql'       => 'varchar(255) NOT NULL default \'\'',
        ],
        'sizes'       => [
            'exclude'   => true,
            'inputType' => 'checkbox',
            'options_callback' => [
                'contao_bootstrap.grid.listeners.dca.grid',
                'getSizes',
            ],
            'reference' => &$GLOBALS['TL_LANG']['tl_bs_grid'],
            'eval'      => [
                'submitOnChange' => true,
                'multiple'       => true,
                'tl_class'       => 'clr',
                'helpwizard'     => true,
            ],
            'sql'       => 'tinyBlob NULL',
        ],
        'align'       => [
            'inputType' => 'select',
            'options'   => ['start', 'center', 'end'],
            'eval'      => [
                'includeBlankOption' => true,
                'chosen'             => true,
                'tl_class'           => 'w50',
            ],
            'sql'       => 'varchar(64) NOT NULL default \'\'',
        ],
        'justify'     => [
            'inputType' => 'select',
            'options'   => ['start', 'center', 'end', 'around', 'between'],
            'eval'      => [
                'includeBlankOption' => true,
                'chosen'             => true,
                'tl_class'           => 'w50',
            ],
            'sql'       => 'varchar(64) NOT NULL default \'\'',
        ],
        'rowClass'    => [
            'exclude'   => true,
            'default'   => '',
            'inputType' => 'text',
            'eval'      => ['tl_class' => 'clr w50'],
            'sql'       => 'varchar(64) NOT NULL default \'\'',
        ],
        'noGutters'   => [
            'exclude'   => true,
            'default'   => '',
            'inputType' => 'checkbox',
            'reference' => &$GLOBALS['TL_LANG']['tl_bs_grid'],
            'eval'      => ['tl_class' => 'w50 m12'],
            'sql'       => 'char(1) NULL',
        ],
    ],
];
