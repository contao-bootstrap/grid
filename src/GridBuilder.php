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

use Contao\StringUtil;
use ContaoBootstrap\Grid\Definition\Column;
use ContaoBootstrap\Grid\Definition\Grid;
use ContaoBootstrap\Grid\Model\GridModel;

/**
 * GridBuilder builds the grid class from the database definition.
 *
 * @package ContaoBootstrap\Grid
 */
final class GridBuilder
{
    /**
     * Grid model.
     *
     * @var GridModel
     */
    private $model;

    /**
     * Cache of grid being built.
     *
     * @var Grid
     */
    private $grid;

    /**
     * Build a grid.
     *
     * @param int $gridId THe grid id.
     *
     * @return Grid
     *
     * @throws \RuntimeException When Grid does not exist.
     */
    public function build(int $gridId): Grid
    {
        $this->loadModel($gridId);
        $this->createGrid();

        return $this->finish();
    }

    /**
     * Load grid model from the database.
     *
     * @param int $gridId THe grid id.
     *
     * @return void
     *
     * @throws \RuntimeException When Grid does not exist.
     */
    protected function loadModel(int $gridId): void
    {
        $model = GridModel::findByPk($gridId);
        if (!$model) {
            throw new \RuntimeException(sprintf('Grid ID "%s" not found', $gridId));
        }

        $this->model = $model;
    }

    /**
     * Create the grid from the model.
     *
     * @return void
     */
    private function createGrid(): void
    {
        $this->grid = new Grid();
        $sizes      = StringUtil::deserialize($this->model->sizes, true);

        $this->buildRow();

        foreach ($sizes as $size) {
            $field      = $size . 'Size';
            $definition = StringUtil::deserialize($this->model->{$field}, true);

            if ($size === 'xs') {
                $size = '';
            }

            $this->buildSize($size, $definition);
        }
    }

    /**
     * Build the row.
     *
     * @return void
     */
    private function buildRow(): void
    {
        if ($this->model->noGutters) {
            $this->grid->addClass('no-gutters');
        }

        if ($this->model->rowClass) {
            $this->grid->addClass($this->model->rowClass);
        }

        if ($this->model->align) {
            $this->grid->align($this->model->align);
        }

        if ($this->model->justify) {
            $this->grid->justify($this->model->justify);
        }
    }

    /**
     * Build a grid size.
     *
     * @param string $size       Grid size.
     * @param array  $definition Definition.
     *
     * @return void
     */
    private function buildSize(string $size, array $definition): void
    {
        foreach ($definition as $columnDefinition) {
            $column = $this->buildColumn($columnDefinition);

            $this->grid->addColumn($column, $size);
        }
    }

    /**
     * Build a column.
     *
     * @param array $definition Column definition.
     *
     * @return Column
     */
    private function buildColumn(array $definition): Column
    {
        $column = new Column();
        if ($definition['width']) {
            switch ($definition['width']) {
                case 'variable':
                    $column->variableWidth();
                    break;
                case 'auto':
                    break;
                case 'null':
                    $column->width(0);
                    break;
                default:
                    $column->width((int) $definition['width']);
            }
        }

        if ($definition['order']) {
            $column->order((int) $definition['order']);
        }

        if ($definition['align']) {
            $column->align($definition['align']);
        }

        if ($definition['offset']) {
            $offset = $this->parseOffset($definition['offset']);
            $column->offset($offset);
        }

        if ($definition['reset']) {
            $column->reset();
        }

        if ($definition['class']) {
            $column->cssClass($definition['class']);
        }

        return $column;
    }

    /**
     * Finish the grid building.
     *
     * @return Grid
     */
    private function finish()
    {
        $grid        = $this->grid;
        $this->grid  = null;
        $this->model = null;

        return $grid;
    }

    /**
     * Parse the offset definition value.
     *
     * @param mixed $offset Raw offset value.
     *
     * @return mixed
     */
    private function parseOffset($offset)
    {
        if ($offset === 'null') {
            $offset = 0;
        } elseif (is_numeric($offset)) {
            $offset = (int) $offset;
        }

        return $offset;
    }
}
