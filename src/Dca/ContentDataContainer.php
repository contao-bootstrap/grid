<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoBootstrap\Grid\Dca;

use Contao\ContentModel;
use Contao\DataContainer;
use ContaoBootstrap\Grid\Model\GridModel;

/**
 * Class ContentDataContainer
 *
 * @package ContaoBootstrap\Grid\Dca
 */
class ContentDataContainer
{
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
     * @param DataContainer|null $dataContainer
     *
     * @return array
     */
    public function getGridParentOptions(DataContainer $dataContainer = null)
    {
        $columns[] = 'tl_content.type = ?';
        $values[]  = 'gridStart';

        if ($dataContainer) {
            $columns[] = 'tl_content.pid = ?';
            $values[]  = $dataContainer->activeRecord->pid;
        }

        $collection = ContentModel::findBy($columns, $values);
        $options    = [];

        if ($collection) {
            foreach ($collection as $model) {
                $options[$model->id] = sprintf('%s [%s]', $model->name, $model->getRelated('bootstrap_grid')->title);
            }
        }

        return $options;
    }
}
