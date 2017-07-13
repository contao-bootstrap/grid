<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoBootstrap\Grid;

use ContaoBootstrap\Grid\Definition\Grid;

/**
 * GridProvider is the main entry point to get grids or grid iterators.
 *
 * @package ContaoBootstrap\Grid
 */
class GridProvider
{
    /**
     * Grid builder.
     *
     * @var GridBuilder
     */
    private $builder;

    /**
     * Map of created grids.
     *
     * @var array
     */
    private $grids = [];

    /**
     * Map of grid iterators.
     *
     * @var array
     */
    private $iterators = [];

    /**
     * GridProvider constructor.
     *
     * @param GridBuilder $builder The grid builder.
     */
    public function __construct(GridBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Get a grid.
     *
     * @param int $gridId Grid id.
     *
     * @return Grid
     *
     * @throws \RuntimeException When grid could not be build.
     */
    public function getGrid($gridId)
    {
        if (!isset($this->grids[$gridId])) {
            $this->grids[$gridId] = $this->builder->build($gridId);
        }

        return $this->grids[$gridId];
    }

    /**
     * Get the grid iterator.
     *
     * @param string $uniqueId Unique id to reference a grid iterator.
     * @param int    $gridId   The grid id.
     *
     * @return GridIterator
     *
     * @throws \RuntimeException When grid could not be build.
     */
    public function getIterator($uniqueId, $gridId = null)
    {
        if (!isset($this->iterators[$uniqueId])) {
            $grid     = $this->getGrid($gridId);
            $iterator = new GridIterator($grid);

            $this->iterators[$uniqueId] = $iterator;
        }

        return $this->iterators[$uniqueId];
    }
}
