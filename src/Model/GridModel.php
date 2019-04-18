<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2019 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0-or-later
 * @filesource
 */

namespace ContaoBootstrap\Grid\Model;

use Contao\Model;

/**
 * Class GridModel.
 *
 * @package ContaoBootstrap\Grid\Model
 *
 * @property string|array sizes
 * @property int          pid
 * @property string       noGutters
 * @property string       rowClass
 * @property string       align
 * @property string       justify
 */
class GridModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_bs_grid';
}
