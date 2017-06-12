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
 * Class GridBuilder
 *
 * @package ContaoBootstrap\Grid
 */
class GridBuilder
{
    /**
     * @var GridModel
     */
    private $model;

    /**
     * @var Grid
     */
    private $grid;

    /**
     * @param $gridId
     *
     * @return Grid
     */
    public function build($gridId)
    {
        $this->loadModel($gridId);
        $this->createGrid();

        return $this->finish();
    }

    /**
     * @param $gridId
     */
    protected function loadModel($gridId)
    {
        $model = GridModel::findByPk($gridId);
        if (!$model) {
            throw new \RuntimeException(sprintf('Grid ID "%s" not found', $gridId));
        }

        $this->model = $model;
    }

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

    private function buildSize($size, $definition)
    {
        foreach ($definition as $columnDefinition) {
            $column = $this->buildColumn($columnDefinition);

            $this->grid->addColumn($column, $size);
        }
    }

    /**
     * @param $columnDefinition
     *
     * @return Column
     */
    private function buildColumn($columnDefinition)
    {
        $column = new Column();
        $column->width($columnDefinition['width']);

        foreach (['offset', 'order', 'align'] as $key) {
            if ($columnDefinition[$key]) {
                $column->{$key}($columnDefinition[$key]);
            }
        }

        if ($columnDefinition['reset']) {
            $column->reset();
        }

        if ($columnDefinition['class']) {
            $column->cssClass($columnDefinition['class']);
        }

        return $column;
    }

    /**
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
