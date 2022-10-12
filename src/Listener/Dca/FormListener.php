<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\DataContainer;
use Contao\FormFieldModel;
use Contao\Model;
use Contao\Model\Collection;

use function sprintf;
use function time;

/**
 * Data container helper class for form.
 *
 * @extends AbstractWrapperDcaListener<FormFieldModel>
 */
final class FormListener extends AbstractWrapperDcaListener
{
    /** {@inheritDoc} */
    protected function getNextElements($current): array
    {
        $collection = FormFieldModel::findBy(
            [
                'tl_form_field.pid=?',
                '(tl_form_field.type != ? AND tl_form_field.bs_grid_parent = ?)',
                'tl_form_field.sorting > ?',
            ],
            [$current->pid, 'bs_gridStop', $current->id, $current->sorting],
            ['order' => 'tl_form_field.sorting ASC'],
        );

        if ($collection instanceof Collection) {
            return $collection->getIterator()->getArrayCopy();
        }

        return [];
    }

    /** {@inheritDoc} */
    protected function getStopElement($current): Model
    {
        $stopElement = FormFieldModel::findOneBy(
            ['tl_form_field.type=?', 'tl_form_field.bs_grid_parent=?'],
            ['bs_gridStop', $current->id],
        );

        if ($stopElement instanceof FormFieldModel) {
            return $stopElement;
        }

        $nextElements = $this->getNextElements($current);
        $stopElement  = $this->createStopElement($current, (int) $current->sorting);
        $this->updateSortings($nextElements, (int) $stopElement->sorting);

        return $stopElement;
    }

    /**
     * {@inheritDoc}
     */
    protected function createGridElement($current, string $type, int &$sorting): Model
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
     * @return array<int|string,string>
     */
    public function getGridParentOptions(DataContainer $dataContainer): array
    {
        $columns = [
            'tl_form_field.type = ?',
            'tl_form_field.pid = ?',
        ];

        $values = ['bs_gridStart', $dataContainer->currentPid];

        $collection = FormFieldModel::findBy($columns, $values);
        $options    = [];

        if ($collection instanceof Collection) {
            foreach ($collection as $model) {
                $related             = $model->getRelated('bs_grid');
                $options[$model->id] = sprintf(
                    '%s [%s]',
                    $model->bs_grid_name,
                    $related ? $related->title : $related->bs_grid,
                );
            }
        }

        return $options;
    }
}
