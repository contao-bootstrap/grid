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

declare(strict_types=1);

namespace ContaoBootstrap\Grid;

use ContaoBootstrap\Grid\Definition\Grid;

/**
 * GridProvider is the main entry point to get grids or grid iterators.
 *
 * @package ContaoBootstrap\Grid
 */
final class GridProvider
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
    public function getGrid(int $gridId): Grid
    {
        if (!isset($this->grids[$gridId])) {
            $this->grids[$gridId] = $this->builder->build($gridId);
        }

        return $this->grids[$gridId];
    }

    /**
     * Get the grid iterator.
     *
     * @param string   $uniqueId Unique id to reference a grid iterator.
     * @param int|null $gridId   The grid id.
     *
     * @return GridIterator
     *
     * @throws \RuntimeException When grid could not be build.
     */
    public function getIterator(string $uniqueId, ?int $gridId = null): GridIterator
    {
        if (!isset($this->iterators[$uniqueId])) {
            $grid     = $this->getGrid($gridId);
            $iterator = new GridIterator($grid);

            $this->iterators[$uniqueId] = $iterator;
        }

        return $this->iterators[$uniqueId];
    }
}
