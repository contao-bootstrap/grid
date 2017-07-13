<?php

/**
 * @package    Website
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoBootstrap\Grid\Component\ContentElement;

use Contao\ContentElement;
use ContaoBootstrap\Grid\GridProvider;

/**
 * Class AbstractGridElement.
 *
 * @package ContaoBootstrap\Grid\Component\ContentElement
 */
abstract class AbstractGridElement extends ContentElement
{
    /**
     * Get the grid provider.
     *
     * @return GridProvider
     */
    protected function getGridProvider()
    {
        return static::getContainer()->get('contao_bootstrap.grid.grid_provider');
    }
}
