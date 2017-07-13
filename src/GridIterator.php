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
 * GridIterator to iterate over the grid columns.
 *
 * @package ContaoBootstrap\Grid
 */
class GridIterator implements \Iterator
{
    /**
     * Grid.
     *
     * @var Grid
     */
    private $grid;

    /**
     * Current index.
     *
     * @var int
     */
    private $index = 0;

    /**
     * GridIterator constructor.
     *
     * @param Grid $grid The grid.
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    public function row()
    {
        return $this->grid->buildRow(true);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->grid->buildColumn($this->index, true);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->index = 0;
    }
}
