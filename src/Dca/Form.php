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

namespace ContaoBootstrap\Grid\Dca;

use Contao\DataContainer;
use Contao\FormFieldModel;
use Contao\Model;

/**
 * Data container helper class for form.
 *
 * @package ContaoBootstrap\Grid\Dca
 */
class Form extends AbstractWrapperDcaHelper
{
    /**
     * Generate the columns.
     *
     * @param int           $value         Number of columns which should be generated.
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return null
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function generateColumns($value, $dataContainer)
    {
        if (!$dataContainer->activeRecord) {
            return null;
        }

        $current = $dataContainer->activeRecord;

        if ($value && $dataContainer->activeRecord) {
            $stopElement  = $this->getStopElement($current);
            $nextElements = $this->getNextElements($stopElement);
            $sorting      = $stopElement->sorting;

            $sorting = $this->createSeparators($value, $current, $sorting);

            array_unshift($nextElements, $stopElement);
            $this->updateSortings($nextElements, $sorting);
        }

        return null;
    }


    /**
     * Get the next content elements.
     *
     * @param FormFieldModel $current Current content model.
     *
     * @return FormFieldModel[]
     */
    protected function getNextElements($current)
    {
        $collection = FormFieldModel::findBy(
            [
                'tl_form_field.pid=?',
                '(tl_form_field.type != ? AND tl_form_field.bs_grid_parent = ?)',
                'tl_form_field.sorting > ?'
            ],
            [$current->pid, 'bs_gridStop', $current->id, $current->sorting],
            ['order' => 'tl_form_field.sorting ASC']
        );

        if ($collection) {
            return $collection->getIterator()->getArrayCopy();
        }

        return [];
    }

    /**
     * Get related stop element.
     *
     * @param FormFieldModel $current Current element.
     *
     * @return FormFieldModel|Model
     */
    protected function getStopElement($current)
    {
        $stopElement = FormFieldModel::findOneBy(
            ['tl_form_field.type=?', 'tl_form_field.bs_grid_parent=?'],
            ['bs_gridStop', $current->id]
        );


        if ($stopElement) {
            return $stopElement;
        }

        $nextElements = $this->getNextElements($current);
        $stopElement  = $this->createStopElement($current, $current->sorting);
        $this->updateSortings($nextElements, $stopElement->sorting);

        return $stopElement;
    }

    /**
     * Create a grid element.
     *
     * @param FormFieldModel $current Current content model.
     * @param string         $type    Type of the content model.
     * @param int            $sorting The sorting value.
     *
     * @return FormFieldModel
     */
    protected function createGridElement($current, $type, &$sorting)
    {
        $model                 = new FormFieldModel();
        $model->tstamp         = time();
        $model->pid            = $current->pid;
        $model->sorting        = $sorting;
        $model->type           = $type;
        $model->bs_grid_parent = $current->id;
        $model->save();

        return $model;
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
        $values[]  = 'bs_gridStart';

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
