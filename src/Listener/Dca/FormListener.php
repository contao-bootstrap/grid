<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\Database\Result;
use Contao\DataContainer;
use Contao\FormFieldModel;
use Contao\Model;
use Contao\Model\Collection;

use function array_unshift;
use function assert;
use function defined;
use function sprintf;
use function time;

/**
 * Data container helper class for form.
 *
 * @extends AbstractWrapperDcaListener<FormFieldModel>
 */
class FormListener extends AbstractWrapperDcaListener
{
    /**
     * Generate the columns.
     *
     * @param int|string    $value         Number of columns which should be generated.
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return null
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function generateColumns($value, $dataContainer)
    {
        if (! $dataContainer->activeRecord) {
            return null;
        }

        $current = $dataContainer->activeRecord;
        assert($current instanceof FormFieldModel || $current instanceof Result);

        if ($value) {
            $stopElement  = $this->getStopElement($current);
            $nextElements = $this->getNextElements($stopElement);
            $sorting      = $stopElement->sorting;

            $sorting = $this->createSeparators((int) $value, $current, $sorting);

            array_unshift($nextElements, $stopElement);
            $this->updateSortings($nextElements, $sorting);
        }

        return null;
    }

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
            ['order' => 'tl_form_field.sorting ASC']
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
            ['bs_gridStop', $current->id]
        );

        if ($stopElement instanceof FormFieldModel) {
            return $stopElement;
        }

        $nextElements = $this->getNextElements($current);
        $stopElement  = $this->createStopElement($current, $current->sorting);
        $this->updateSortings($nextElements, $stopElement->sorting);

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
    public function getGridParentOptions(): array
    {
        $columns = [
            'tl_form_field.type = ?',
            'tl_form_field.pid = ?',
        ];

        assert(defined('CURRENT_ID'));
        $values = ['bs_gridStart', CURRENT_ID];

        $collection = FormFieldModel::findBy($columns, $values);
        $options    = [];

        if ($collection instanceof Collection) {
            foreach ($collection as $model) {
                $related             = $model->getRelated('bs_grid');
                $options[$model->id] = sprintf(
                    '%s [%s]',
                    $model->bs_grid_name,
                    $related ? $related->title : $related->bs_grid
                );
            }
        }

        return $options;
    }
}
