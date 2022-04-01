<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\FormField;

use Contao\FormFieldModel;
use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\GridIterator;

abstract class AbstractRelatedFormField extends AbstractFormField
{
    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        return '';
    }

    protected function getIterator(): ?GridIterator
    {
        $provider = $this->getGridProvider();
        $parent   = $this->getParent();

        if ($parent) {
            try {
                $iterator = $provider->getIterator('ffl:' . $parent->id, (int) $parent->bs_grid);
                $this->getResponseTagger()->addTags(['contao.db.tl_bs_grid.' . $parent->bs_grid]);

                return $iterator;
            } catch (GridNotFound $e) {
                // Do nothing. Error is displayed in backend view.
                return null;
            }
        }

        return null;
    }

    /**
     * Get the parent model.
     */
    protected function getParent(): ?FormFieldModel
    {
        return FormFieldModel::findByPk($this->bs_grid_parent);
    }
}
