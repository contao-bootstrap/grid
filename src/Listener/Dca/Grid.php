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

namespace ContaoBootstrap\Grid\Listener\Dca;

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
    public function enterContext(): void
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
    public function initializePalette(): void
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
    public function generateLabel(array $row): string
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
    public function getWidths(): array
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
    public function getOrders(): array
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
    public function getOffsets(): array
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
    private function getColumns(): int
    {
        return (int) $this->environment->getConfig()->get('grid.columns', 12);
    }
}
