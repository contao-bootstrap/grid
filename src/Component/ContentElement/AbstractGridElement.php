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

namespace ContaoBootstrap\Grid\Component\ContentElement;

use Contao\ContentElement;
use ContaoBootstrap\Grid\Component\ComponentTrait;

/**
 * Class AbstractGridElement.
 *
 * @package ContaoBootstrap\Grid\Component\ContentElement
 */
abstract class AbstractGridElement extends ContentElement
{
    use ComponentTrait;
}
