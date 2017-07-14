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
use Contao\FormFieldModel;

class Form extends AbstractDcaHelper
{
    /**
     * Generate the columns.
     *
     * @param int           $value         Number of columns which should be generated.
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return null
     */
    public function generateColumns($value, $dataContainer)
    {
        return null;
    }

    /**
     * Get all grid parent options.
     *
     * @param DataContainer|null $dataContainer Data container driver.
     *
     * @return array
     */
    public function getGridParentOptions(DataContainer $dataContainer = null)
    {
        $columns[] = 'tl_form_field.type = ?';
        $values[]  = 'gridStart';

        if ($dataContainer) {
            $columns[] = 'tl_form_field.pid = ?';
            $values[]  = $dataContainer->activeRecord->pid;
        }

        $collection = FormFieldModel::findBy($columns, $values);
        $options    = [];

        if ($collection) {
            foreach ($collection as $model) {
                $options[$model->id] = sprintf(
                    '%s [%s]',
                    $model->name,
                    $model->getRelated('bs_grid')->title
                );
            }
        }

        return $options;
    }
}
