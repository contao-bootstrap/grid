<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0
 * @filesource
 */

use ContaoBootstrap\Grid\Component\ContentElement\GridSeparatorElement;
use ContaoBootstrap\Grid\Component\ContentElement\GridStartElement;
use ContaoBootstrap\Grid\Component\ContentElement\GridStopElement;
use ContaoBootstrap\Grid\Component\FormField\GridSeparatorFormField;
use ContaoBootstrap\Grid\Component\FormField\GridStartFormField;
use ContaoBootstrap\Grid\Component\FormField\GridStopFormField;
use ContaoBootstrap\Grid\Component\Module\GridModule;
use ContaoBootstrap\Grid\Model\GridModel;

// Models
$GLOBALS['TL_MODELS']['tl_bs_grid'] = GridModel::class;

// Backend modules
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_bs_grid';

// Frontend modules
$GLOBALS['FE_MOD']['miscellaneous']['bs_grid'] = GridModule::class;

// Content elements
$GLOBALS['TL_CTE']['bs_grid']['bs_gridStart']     = GridStartElement::class;
$GLOBALS['TL_CTE']['bs_grid']['bs_gridStop']      = GridStopElement::class;
$GLOBALS['TL_CTE']['bs_grid']['bs_gridSeparator'] = GridSeparatorElement::class;

// Form fields
$GLOBALS['TL_FFL']['bs_gridStart']     = GridStartFormField::class;
$GLOBALS['TL_FFL']['bs_gridSeparator'] = GridSeparatorFormField::class;
$GLOBALS['TL_FFL']['bs_gridStop']      = GridStopFormField::class;

// Wrapper elements
$GLOBALS['TL_WRAPPERS']['start'][]     = 'bs_gridStart';
$GLOBALS['TL_WRAPPERS']['separator'][] = 'bs_gridSeparator';
$GLOBALS['TL_WRAPPERS']['stop'][]      = 'bs_gridStop';

// Hooks
$GLOBALS['TL_HOOKS']['exportTheme'][] = [
    'contao_bootstrap.grid.listeners.theme_export',
    'onExportTheme'
];

$GLOBALS['TL_HOOKS']['extractThemeFiles'][] = [
    'contao_bootstrap.grid.listeners.theme_import',
    'onExtractThemeFiles'
];

// Easy Themes
$GLOBALS['TL_EASY_THEMES_MODULES']['bs_grid'] = [
    'href_fragment' => 'table=tl_bs_grid',
];
