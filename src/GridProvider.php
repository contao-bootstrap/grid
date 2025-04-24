<?php

/**
 * Contao Bootstrap grid.
 *
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid;

use ContaoBootstrap\Grid\Definition\Grid;
use InvalidArgumentException;
use RuntimeException;

/**
 * GridProvider is the main entry point to get grids or grid iterators.
 */
final class GridProvider
{
    /**
     * Grid builder.
     */
    private GridBuilder $builder;

    /**
     * Map of created grids.
     *
     * @var array<int,Grid>
     */
    private array $grids = [];

    /**
     * Map of grid iterators.
     *
     * @var array<string,GridIterator>
     */
    private array $iterators = [];

    /** @param GridBuilder $builder The grid builder. */
    public function __construct(GridBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Get a grid.
     *
     * @param int $gridId Grid id.
     *
     * @throws RuntimeException When grid could not be build.
     */
    public function getGrid(int $gridId): Grid
    {
        if (! isset($this->grids[$gridId])) {
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
     * @throws RuntimeException When grid could not be build.
     */
    public function getIterator(string $uniqueId, int|null $gridId = null): GridIterator
    {
        if (! isset($this->iterators[$uniqueId])) {
            if ($gridId === null) {
                throw new InvalidArgumentException('GridId required if iterator does not yet exist');
            }

            $grid     = $this->getGrid($gridId);
            $iterator = new GridIterator($grid);

            $this->iterators[$uniqueId] = $iterator;
        }

        return $this->iterators[$uniqueId];
    }
}
