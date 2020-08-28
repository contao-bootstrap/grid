<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Patrick Landolt <patrick.landolt@artack.ch>
 * @copyright  2017-2020 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid;

use Contao\StringUtil;
use ContaoBootstrap\Core\Environment;
use ContaoBootstrap\Grid\Definition\Column;
use ContaoBootstrap\Grid\Definition\Grid;
use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\Model\GridModel;
use RuntimeException;
use function array_search;

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
     * Core Environment.
     *
     * @var Environment
     */
    private $environment;

    /**
     * GridBuilder constructor.
     *
     * @param Environment $environment The Core Environment.
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Build a grid.
     *
     * @param int $gridId THe grid id.
     *
     * @return Grid
     *
     * @throws RuntimeException When Grid does not exist.
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
     * @throws GridNotFound When Grid does not exist.
     */
    protected function loadModel(int $gridId): void
    {
        $model = GridModel::findByPk($gridId);
        if (!$model) {
            throw GridNotFound::withId($gridId);
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

            if ($size === $this->environment->getConfig()->get('grid.default_size', 'xs')) {
                $size = '';
            }

            $this->buildSize($size, $definition, $sizes);
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
     * @param array  $sizes      List of defined sizes.
     *
     * @return void
     */
    private function buildSize(string $size, array $definition, array $sizes): void
    {
        foreach ($definition as $columnDefinition) {
            $column = $this->buildColumn($columnDefinition, $size, $sizes);

            $this->grid->addColumn($column, $size);
        }
    }

    /**
     * Build a column.
     *
     * @param array  $definition Column definition.
     * @param string $size       The column size.
     * @param array  $sizes      List of defined sizes.
     *
     * @return Column
     */
    private function buildColumn(array $definition, string $size, array $sizes): Column
    {
        $column = new Column();

        $this->buildColumnWidth($definition, $column);
        $this->buildColumnResets($definition, $column, $size, $sizes);

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
    private function finish(): Grid
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

    /**
     * Build the column width.
     *
     * @param array  $definition The grid column definition.
     * @param Column $column     The column.
     *
     * @return void
     */
    private function buildColumnWidth(array $definition, Column $column): void
    {
        if ($definition['width']) {
            switch ($definition['width']) {
                case 'variable':
                    $column->variableWidth();
                    break;
                case 'auto':
                case 'equal':
                    break;
                case 'null':
                    $column->width(0);
                    break;
                default:
                    $column->width((int) $definition['width']);
            }
        }
    }

    /**
     * Build the column resets.
     *
     * @param array  $definition The grid column definition.
     * @param Column $column     The column.
     * @param string $size       The column size.
     * @param array  $sizes      List of defined sizes.
     *
     * @return void
     */
    private function buildColumnResets(array $definition, Column $column, string $size, array $sizes): void
    {
        switch ($definition['reset']) {
            case '2':
                $key  = array_search($size, $sizes);
                $next = ($sizes[($key + 1)] ?? null);

                if ($next) {
                    $column->limitedReset((string) $next);

                    break;
                }

                // No break here,
            case '1':
                $column->reset();
                break;

            default:
                // Do nothing.
        }
    }
}
