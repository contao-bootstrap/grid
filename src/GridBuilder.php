<?php

/**
 * Contao Bootstrap grid.
 *
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
use function is_numeric;

/**
 * GridBuilder builds the grid class from the database definition.
 */
final class GridBuilder
{
    /**
     * Grid model.
     */
    private GridModel $model;

    /**
     * Cache of grid being built.
     */
    private Grid $grid;

    /**
     * Core Environment.
     */
    private Environment $environment;

    /**
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
     * @throws GridNotFound When Grid does not exist.
     */
    protected function loadModel(int $gridId): void
    {
        $model = GridModel::findByPk($gridId);
        if (! $model) {
            throw GridNotFound::withId($gridId);
        }

        $this->model = $model;
    }

    /**
     * Create the grid from the model.
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

        if (! $this->model->justify) {
            return;
        }

        $this->grid->justify($this->model->justify);
    }

    /**
     * Build a grid size.
     *
     * @param string                    $size       Grid size.
     * @param list<array<string,mixed>> $definition Definition.
     * @param list<string>              $sizes      List of defined sizes.
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
     * @param array<string,mixed> $definition Column definition.
     * @param string              $size       The column size.
     * @param list<string>        $sizes      List of defined sizes.
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
     * @param array<string,mixed> $definition The grid column definition.
     * @param Column              $column     The column.
     */
    private function buildColumnWidth(array $definition, Column $column): void
    {
        if (! $definition['width']) {
            return;
        }

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

    /**
     * Build the column resets.
     *
     * @param array<string,mixed> $definition The grid column definition.
     * @param Column              $column     The column.
     * @param string              $size       The column size.
     * @param list<string>        $sizes      List of defined sizes.
     */
    private function buildColumnResets(array $definition, Column $column, string $size, array $sizes): void
    {
        switch ($definition['reset']) {
            case '2':
                $key  = array_search($size, $sizes);
                $next = ($sizes[$key + 1] ?? null);

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
