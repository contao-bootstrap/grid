<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Definition;

use function array_key_exists;
use function array_unique;
use function array_values;
use function count;
use function explode;
use function implode;
use function in_array;

class Grid
{
    /**
     * Columns list for each size.
     *
     * @var Column[][]
     */
    private array $columns = [];

    /**
     * Grid alignment.
     */
    private string $align;

    /**
     * Grid justify settings.
     */
    private string $justify;

    /**
     * Row classes.
     *
     * @var list<string>
     */
    private array $rowClasses = ['row'];

    /**
     * Show gutters.
     */
    private bool $noGutters = false;

    /**
     * Add a column.
     *
     * @param Column|null $column New column.
     * @param string      $size   Column size.
     */
    public function addColumn(?Column $column = null, string $size = ''): Column
    {
        if (! $column) {
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
            if (in_array($class, $this->rowClasses)) {
                continue;
            }

            $this->rowClasses[] = $class;
        }

        return $this;
    }

    /**
     * Build the row.
     *
     * @param bool $flat If true a string is returned.
     *
     * @return list<string>|string
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
     * @return list<string>|string
     */
    public function buildColumn(int $index, bool $flat = false)
    {
        $classes = [];
        foreach ($this->columns as $size => $columns) {
            $column = $this->getColumnByIndex($columns, $index);

            if (! $column) {
                continue;
            }

            $classes = $column->build($classes, $size);
        }

        $classes = array_values(array_unique($classes));

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
     * @return list<string>
     */
    public function buildResets(int $index): array
    {
        $resets = [];

        foreach ($this->columns as $size => $columns) {
            $column = $this->getColumnByIndex($columns, $index);

            if (! $column) {
                continue;
            }

            $resets = $column->buildReset($resets, $size);
        }

        return $resets;
    }

    /**
     * Get a column by index.
     *
     * @param Column[] $columns Column.
     * @param int      $index   Column index.
     */
    private function getColumnByIndex(array $columns, int $index): ?Column
    {
        $currentIndex = $index;

        if (! array_key_exists($currentIndex, $columns) && $currentIndex > 0) {
            $currentIndex %= count($columns);
        }

        if (array_key_exists($currentIndex, $columns)) {
            return $columns[$currentIndex];
        }

        return null;
    }
}
