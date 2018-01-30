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

use Contao\DataContainer;
use ContaoBootstrap\Core\Environment;
use ContaoBootstrap\Grid\Model\GridModel;

/**
 * GridOptionsProvider provides grid related options callbacks.
 *
 * @package ContaoBootstrap\Grid\Dca
 */
abstract class AbstractDcaListener
{
    /**
     * Bootstrap environment.
     *
     * @var Environment
     */
    private $environment;

    /**
     * Form constructor.
     *
     * @param Environment $environment Bootstrap environment.
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Get range of grid columns.
     *
     * @return array
     */
    public function getGridColumns(): array
    {
        return range(
            1,
            (int) $this->environment->getConfig()->get('grid.columns', 12)
        );
    }

    /**
     * Get all available grids.
     *
     * @return array
     */
    public function getGridOptions(): array
    {
        $collection = GridModel::findAll(['order' => 'tl_bs_grid.title']);
        $options    = [];

        if ($collection) {
            foreach ($collection as $model) {
                $parent = sprintf(
                    '%s [ID %s]',
                    $model->getRelated('pid')->name,
                    $model->pid
                );

                $options[$parent][$model->id] = sprintf('%s [ID %s]', $model->title, $model->id);
            }
        }

        return $options;
    }

    /**
     * Generate a grid name if not given.
     *
     * @param string        $value         Grid name.
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return string
     */
    public function generateGridName($value, $dataContainer): string
    {
        if (!$value) {
            $value = 'grid_' . $dataContainer->activeRecord->id;
        }

        return $value;
    }
}
