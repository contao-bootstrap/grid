<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoBootstrap\Grid;


class GridProvider
{
    /**
     * @var GridBuilder
     */
    private $builder;

    private $grids = [];

    private $iterators = [];

    /**
     * GridProvider constructor.
     *
     * @param GridBuilder $builder
     */
    public function __construct(GridBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function getGrid($gridId)
    {
        if (!isset($this->grids[$gridId])) {
            $this->grids[$gridId] = $this->builder->build($gridId);
        }

        return $this->grids[$gridId];
    }

    /**
     * @param      $uniqueId
     * @param null $gridId
     *
     * @return GridIterator
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
