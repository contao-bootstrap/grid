<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoBootstrap\Grid\Definition;

/**
 * Class Grid.
 *
 * @package ContaoBootstrap\Grid\Definition
 */
class Grid
{
    /**
     * @var Column[][]
     */
    private $columns;

    private $align;
    private $justify;
    private $rowClasses = ['row'];
    private $noGutters;

    /**
     * Add a column.
     *
     * @param Column|null $column
     * @param string      $size
     *
     * @return string
     */
    public function addColumn(Column $column = null, $size = '')
    {
        if (!$column) {
            $column = new Column();
        }

        $this->columns[$size][] = $column;

        return $column;
    }

    /**
     * @param bool $flat
     *
     * @return array|string
     */
    public function buildRow($flat = false)
    {
        $classes = $this->rowClasses;

        if ($this->align) {
            $classes[] = 'align-items-' . $this->align;
        }

        if ($this->justify) {
            $classes[] = 'justify-' . $this->justify;
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
     * @param      $index
     * @param bool $flat
     *
     * @return array|string
     */
    public function buildColumn($index, $flat = false)
    {
        $classes = [];
        foreach ($this->columns as $size => $columns) {
            $currentIndex = $index;

            if (!array_key_exists($currentIndex, $columns) && $currentIndex > 0) {
                $currentIndex = count($columns) % $currentIndex;
            }

            if (array_key_exists($currentIndex, $columns)) {
                $classes = $columns[$currentIndex]->build($classes, $size);
            }
        }

        $classes = array_unique($classes);

        if ($flat) {
            return implode(' ', $classes);
        }

        return $classes;
    }
}
