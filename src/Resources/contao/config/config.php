<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Patrick Landolt <patrick.landolt@artack.ch>
 * @copyright  2017-2020 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0-or-later
 * @filesource
 */

use ContaoBootstrap\Grid\Component\FormField\GridSeparatorFormField;
use ContaoBootstrap\Grid\Component\FormField\GridStartFormField;
use ContaoBootstrap\Grid\Component\FormField\GridStopFormField;
use ContaoBootstrap\Grid\Listener\Hook\GridSizesListener;
use ContaoBootstrap\Grid\Listener\Hook\NewsGridListener;
use ContaoBootstrap\Grid\Model\GridModel;
use Netzmacht\Contao\Toolkit\Component\ContentElement\ContentElementDecorator;

// Models
$GLOBALS['TL_MODELS']['tl_bs_grid'] = GridModel::class;

// Backend modules
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_bs_grid';

array_insert(
    $GLOBALS['TL_CTE']['media'],
    (array_search('gallery', array_keys($GLOBALS['TL_CTE']['media'])) + 1),
    [
        'bs_grid_gallery' => ContentElementDecorator::class
    ]
);

// Form fields
$GLOBALS['TL_FFL']['bs_gridStart']     = GridStartFormField::class;
$GLOBALS['TL_FFL']['bs_gridSeparator'] = GridSeparatorFormField::class;
$GLOBALS['TL_FFL']['bs_gridStop']      = GridStopFormField::class;

// Wrapper elements
$GLOBALS['TL_WRAPPERS']['start'][]     = 'bs_gridStart';
$GLOBALS['TL_WRAPPERS']['separator'][] = 'bs_gridSeparator';
$GLOBALS['TL_WRAPPERS']['stop'][]      = 'bs_gridStop';

// Hooks
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = [GridSizesListener::class, 'initializeSizes'];

$GLOBALS['TL_HOOKS']['exportTheme'][] = [
    'contao_bootstrap.grid.listeners.theme_export',
    'onExportTheme'
];

$GLOBALS['TL_HOOKS']['extractThemeFiles'][] = [
    'contao_bootstrap.grid.listeners.theme_import',
    'onExtractThemeFiles'
];

$GLOBALS['TL_HOOKS']['parseArticles'][] = [NewsGridListener::class, 'onParseArticles'];

// Easy Themes
$GLOBALS['TL_EASY_THEMES_MODULES']['bs_grid'] = [
    'href_fragment' => 'table=tl_bs_grid',
];
