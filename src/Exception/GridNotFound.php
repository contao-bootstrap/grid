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

namespace ContaoBootstrap\Grid\Exception;

use RuntimeException;

/**
 * Class GridNotFound
 */
class GridNotFound extends RuntimeException
{
    /**
     * Create the exception with a predefined message.
     *
     * @param int $gridId The grid id.
     *
     * @return GridNotFound
     */
    public static function withId(int $gridId) : self
    {
        return new self(sprintf('Grid with ID "%s" not found', $gridId));
    }
}
