<?php

declare(strict_types=1);

use ContaoBootstrap\Grid\Component\FormField\GridSeparatorFormField;
use ContaoBootstrap\Grid\Component\FormField\GridStartFormField;
use ContaoBootstrap\Grid\Component\FormField\GridStopFormField;
use ContaoBootstrap\Grid\Model\GridModel;

// Models
$GLOBALS['TL_MODELS']['tl_bs_grid'] = GridModel::class;

// Backend modules
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_bs_grid';

// Form fields
$GLOBALS['TL_FFL']['bs_gridStart']     = GridStartFormField::class;
$GLOBALS['TL_FFL']['bs_gridSeparator'] = GridSeparatorFormField::class;
$GLOBALS['TL_FFL']['bs_gridStop']      = GridStopFormField::class;

// Wrapper elements
$GLOBALS['TL_WRAPPERS']['start'][]     = 'bs_gridStart';
$GLOBALS['TL_WRAPPERS']['separator'][] = 'bs_gridSeparator';
$GLOBALS['TL_WRAPPERS']['stop'][]      = 'bs_gridStop';

// Easy Themes
$GLOBALS['TL_EASY_THEMES_MODULES']['bs_grid'] = ['href_fragment' => 'table=tl_bs_grid'];
