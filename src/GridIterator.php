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

declare(strict_types=1);

namespace ContaoBootstrap\Grid;

use ContaoBootstrap\Grid\Definition\Grid;

/**
 * GridIterator to iterate over the grid columns.
 *
 * @package ContaoBootstrap\Grid
 */
final class GridIterator implements \Iterator
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

    /**
     * Build a row.
     *
     * @return string
     */
    public function row(): string
    {
        return $this->grid->buildRow(true);
    }

    /**
     * Get all resets.
     *
     * @return array
     */
    public function resets(): array
    {
        return $this->grid->buildResets($this->index);
    }

    /**
     * {@inheritdoc}
     */
    public function current(): string
    {
        return $this->grid->buildColumn($this->index, true);
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
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
    public function valid(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        $this->index = 0;
    }
}
