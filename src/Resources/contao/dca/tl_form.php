<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2020 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

use ContaoBootstrap\Grid\Listener\Dca\FormFixFormFieldParentRelationsListener;

/*
 * Config
 */

$GLOBALS['TL_DCA']['tl_form']['config']['oncopy_callback'][] = [
    FormFixFormFieldParentRelationsListener::class,
    'onCopy'
];
