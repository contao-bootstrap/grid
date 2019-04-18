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

namespace ContaoBootstrap\Grid\Definition;

/**
 * Class Grid.
 *
 * @package ContaoBootstrap\Grid\Definition
 */
class Grid
{
    /**
     * Columns list for each size.
     *
     * @var Column[][]
     */
    private $columns = [];

    /**
     * Grid alignment.
     *
     * @var string
     */
    private $align;

    /**
     * Grid justify settings.
     *
     * @var string
     */
    private $justify;

    /**
     * Row classes.
     *
     * @var array
     */
    private $rowClasses = ['row'];

    /**
     * Show gutters.
     *
     * @var bool
     */
    private $noGutters = false;

    /**
     * Add a column.
     *
     * @param Column|null $column New column.
     * @param string      $size   Column size.
     *
     * @return Column
     */
    public function addColumn(Column $column = null, string $size = ''): Column
    {
        if (!$column) {
            $column = new Column();
        }

        $this->columns[$size][] = $column;

        return $column;
    }

    /**
     * Set vertical align value.
     *
     * @param string $align Vertical align value.
     *
     * @return Grid
     */
    public function align(string $align): self
    {
        $this->align = $align;

        return $this;
    }

    /**
     * Set justify.
     *
     * @param string $justify Justify value.
     *
     * @return Grid
     */
    public function justify(string $justify): self
    {
        $this->justify = $justify;

        return $this;
    }

    /**
     * Add a class to the row.
     *
     * @param string $class Row class.
     *
     * @return Grid
     */
    public function addClass(string $class): self
    {
        $classes = explode(' ', $class);

        foreach ($classes as $class) {
            if (!in_array($class, $this->rowClasses)) {
                $this->rowClasses[] = $class;
            }
        }

        return $this;
    }

    /**
     * Build the row.
     *
     * @param bool $flat If true a string is returned.
     *
     * @return array|string
     */
    public function buildRow(bool $flat = false)
    {
        $classes = $this->rowClasses;

        if ($this->align) {
            $classes[] = 'align-items-' . $this->align;
        }

        if ($this->justify) {
            $classes[] = 'justify-content-' . $this->justify;
        }

        if ($this->noGutters) {
            $classes[] = 'no-gutters';
        }

        if ($flat) {
            return implode(' ', $classes);
        }

        return $classes;
    }

    /**
     * Build a column.
     *
     * @param int  $index Current index.
     * @param bool $flat  If true a string is returned.
     *
     * @return array|string
     */
    public function buildColumn(int $index, bool $flat = false)
    {
        $classes = [];
        foreach ($this->columns as $size => $columns) {
            $column = $this->getColumnByIndex($columns, $index);

            if ($column) {
                $classes = $column->build($classes, $size);
            }
        }

        $classes = array_unique($classes);

        if ($flat) {
            return implode(' ', $classes);
        }

        return $classes;
    }

    /**
     * Build reset classes.
     *
     * @param int $index Column index.
     *
     * @return array
     */
    public function buildResets(int $index): array
    {
        $resets = [];

        foreach ($this->columns as $size => $columns) {
            $column = $this->getColumnByIndex($columns, $index);

            if ($column) {
                $resets = $column->buildReset($resets, $size);
            }
        }

        return $resets;
    }

    /**
     * Get a column by index.
     *
     * @param Column[] $columns Column.
     * @param int      $index   Column index.
     *
     * @return null|Column
     */
    private function getColumnByIndex(array $columns, int $index):? Column
    {
        $currentIndex = $index;

        if (!array_key_exists($currentIndex, $columns) && $currentIndex > 0) {
            $currentIndex = ($currentIndex % count($columns));
        }

        if (array_key_exists($currentIndex, $columns)) {
            return $columns[$currentIndex];
        }

        return null;
    }
}
