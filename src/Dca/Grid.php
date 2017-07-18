<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoBootstrap\Grid\Dca;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use ContaoBootstrap\Core\Environment;
use ContaoBootstrap\Core\Environment\ThemeContext;
use ContaoBootstrap\Grid\Model\GridModel;

/**
 * Data container helper for grid.
 *
 * @package ContaoBootstrap\Grid\Dca
 */
class Grid
{
    /**
     * Bootstrap environment.
     *
     * @var Environment
     */
    private $environment;

    /**
     * Constructor.
     *
     * @param Environment $environment Bootstrap environment.
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Enter a bootstrap environment context.
     *
     * @return void
     */
    public function enterContext()
    {
        if (\Input::get('act') === 'edit') {
            $model = GridModel::findByPk(\Input::get('id'));

            if ($model) {
                $this->environment->enterContext(ThemeContext::forTheme($model->pid));
            }
        }
    }

    /**
     * Initialize the palette.
     *
     * @return void
     */
    public function initializePalette()
    {
        if (\Input::get('act') === 'edit') {
            $model = GridModel::findByPk(\Input::get('id'));
            $sizes = array_map(
                function ($value) {
                    return $value . 'Size';
                },
                deserialize($model->sizes, true)
            );

            PaletteManipulator::create()
                ->addField($sizes, 'sizes')
                ->applyToPalette('default', 'tl_bs_grid');
        }
    }

    /**
     * Generate the label.
     *
     * @param array $row Data row.
     *
     * @return string
     */
    public function generateLabel($row)
    {
        return sprintf(
            '%s <div class="tl_gray">%s</div>',
            $row['title'],
            $row['description']
        );
    }

    /**
     * Get all widths.
     *
     * @return array
     */
    public function getWidths()
    {
        $columns = $this->getColumns();
        $values  = ['auto'];

        return array_merge($values, range(0, $columns));
    }

    /**
     * Get the order options.
     *
     * @return array
     */
    public function getOrders()
    {
        $columns = $this->getColumns();
        $values  = [
            'flex' => ['unordered', 'first', 'last'],
            'push' => [],
            'pull' => [],
        ];

        for ($i = 0; $i <= $columns; $i++) {
            $values['push'][] = 'push-' . $i;
            $values['pull'][] = 'pull-' . $i;
        }

        return $values;
    }

    /**
     * Get offset values.
     *
     * @return array
     */
    public function getOffsets()
    {
        $columns = $this->getColumns();
        $values  = array_merge(
            ['null'],
            range(1, $columns)
        );

        return $values;
    }

    /**
     * Get the number of defined columns.
     *
     * @return int
     */
    private function getColumns()
    {
        return $this->environment->getConfig()->get('grid.columns', 12);
    }
}
