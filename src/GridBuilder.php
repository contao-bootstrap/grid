<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoBootstrap\Grid;

use ContaoBootstrap\Grid\Definition\Column;
use ContaoBootstrap\Grid\Definition\Grid;
use ContaoBootstrap\Grid\Model\GridModel;

/**
 * GridBuilder builds the grid class from the database definition.
 *
 * @package ContaoBootstrap\Grid
 */
class GridBuilder
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
    public function build($gridId)
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
    protected function loadModel($gridId)
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
    private function createGrid()
    {
        $this->grid = new Grid();
        $sizes      = deserialize($this->model->sizes, true);

        foreach ($sizes as $size) {
            $field      = $size . 'Size';
            $definition = deserialize($this->model->{$field}, true);

            if ($size === 'xs') {
                $size = '';
            }

            $this->buildSize($size, $definition);
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
    private function buildSize($size, array $definition)
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
    private function buildColumn(array $definition)
    {
        $column = new Column();
        $column->width($definition['width']);

        foreach (['order', 'align'] as $key) {
            if ($definition[$key]) {
                $column->{$key}($definition[$key]);
            }
        }

        if ($definition['offset']) {
            if ($definition['offset'] === 'null') {
                $definition['offset'] = 0;
            }

            $column->offset($definition['offset']);
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
}
