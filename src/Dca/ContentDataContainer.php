<?php

/**
 * @package    Website
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoBootstrap\Grid\Dca;


use ContaoBootstrap\Grid\Model\GridModel;

class ContentDataContainer
{
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
}
