<?php

/**
 * @package    Website
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoBootstrap\Grid\Dca;

use Contao\DataContainer;
use ContaoBootstrap\Core\Config;
use ContaoBootstrap\Grid\Model\GridModel;

/**
 * GridOptionsProvider provides grid related options callbacks.
 *
 * @package ContaoBootstrap\Grid\Dca
 */
abstract class AbstractDcaHelper
{
    /**
     * Bootstrap config.
     *
     * @var Config
     */
    private $config;

    /**
     * Form constructor.
     *
     * @param Config $config Bootstrap config.
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get range of grid columns.
     *
     * @return array
     */
    public function getGridColumns()
    {
        return range(
            1,
            (int) $this->config->get('grid.columns', 12)
        );
    }

    /**
     * Get all available grids.
     *
     * @return array
     */
    public function getGridOptions()
    {
        $collection = GridModel::findAll();
        $options    = [];

        if ($collection) {
            foreach ($collection as $model) {
                $options[$model->id] = sprintf('%s [%s]', $model->title, $model->getRelated('pid')->name);
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
    public function generateGridName($value, $dataContainer)
    {
        if (!$value) {
            $value = 'grid_' . $dataContainer->activeRecord->id;
        }

        return $value;
    }
}
