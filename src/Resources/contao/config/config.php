<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

use ContaoBootstrap\Grid\Component\ContentElement\GridSeparatorElement;
use ContaoBootstrap\Grid\Component\ContentElement\GridStartElement;
use ContaoBootstrap\Grid\Component\ContentElement\GridStopElement;
use ContaoBootstrap\Grid\Model\GridModel;

// Models
$GLOBALS['TL_MODELS']['tl_grid'] = GridModel::class;

// Modules
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_grid';

// Content elements
$GLOBALS['TL_CTE']['bootstrap_grid']['gridStart']     = GridStartElement::class;
$GLOBALS['TL_CTE']['bootstrap_grid']['gridStop']      = GridStopElement::class;
$GLOBALS['TL_CTE']['bootstrap_grid']['gridSeparator'] = GridSeparatorElement::class;

// Wrapper elements
$GLOBALS['TL_WRAPPERS']['start'][]     = 'gridStart';
$GLOBALS['TL_WRAPPERS']['separator'][] = 'gridSeparator';
$GLOBALS['TL_WRAPPERS']['stop'][]      = 'gridStop';
