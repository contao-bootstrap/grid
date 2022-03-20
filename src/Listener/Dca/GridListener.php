<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2020 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\Input;
use Contao\StringUtil;
use Contao\ThemeModel;
use ContaoBootstrap\Core\Environment;
use ContaoBootstrap\Core\Environment\ThemeContext;
use ContaoBootstrap\Grid\Model\GridModel;

/**
 * Data container helper for grid.
 *
 * @package ContaoBootstrap\Grid\Dca
 */
class GridListener
{
    /**
     * Bootstrap environment.
     *
     * @var Environment
     */
    private Environment $environment;

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
        if (Input::get('act') === 'edit') {
            $model = GridModel::findByPk(Input::get('id'));

            if ($model) {
                $this->environment->enterContext(ThemeContext::forTheme((int) $model->pid));
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
        if (Input::get('act') === 'edit') {
            $model = GridModel::findByPk(Input::get('id'));
            $sizes = array_map(
                function ($value) {
                    return $value . 'Size';
                },
                StringUtil::deserialize($model->sizes, true)
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
     * Get all sizes.
     *
     * @return array
     */
    public function getSizes(): array
    {
        $sizes = [];
        if (Input::get('act') === 'edit') {
            $theme = ThemeModel::findByPk(CURRENT_ID);
            $sizes = StringUtil::deserialize($theme->bs_grid_sizes, true);

            if (!$sizes) {
                $sizes = $this->environment->getConfig()->get('grid.sizes', []);
            }

            return $sizes;
        }

        $themes = ThemeModel::findAll() ?: [];
        foreach ($themes as $theme) {
            $sizes = array_merge($sizes, StringUtil::deserialize($theme->bs_grid_sizes, true));
        }

        return array_values(array_unique($sizes));
    }

    /**
     * Get all widths.
     *
     * @return array
     */
    public function getWidths(): array
    {
        $columns = $this->getColumns();
        $values  = ['equal', 'variable', 'null'];
        $values  = array_merge($values, range(1, $columns));

        return array_combine($values, $values);
    }

    /**
     * Get the order options.
     *
     * @return array
     */
    public function getOrders(): array
    {
        $columns = $this->getColumns();

        return range(1, $columns);
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

        return [
            'align' => [
                'ml-auto',
                'mr-auto'
            ],
            'offset' => $values
        ];
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
