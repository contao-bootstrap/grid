<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\FormField;

class GridSeparatorFormField extends AbstractRelatedFormField
{
    /**
     * Template name.
     */
    protected string $strTemplate = 'form_bs_gridSeparator';

    /**
     * {@inheritdoc}
     */
    public function parse($attributes = null): string
    {
        $iterator = $this->getIterator();
        if ($iterator) {
            $iterator->next();
        }

        if ($this->isBackendRequest()) {
            return $this->renderBackendView($this->getParent(), $iterator);
        }

        if ($iterator) {
            $this->rowClasses    = $iterator->row();
            $this->columnClasses = $iterator->current();
            $this->resets        = $iterator->resets();
        } else {
            $this->resets = [];
        }

        return parent::parse($attributes);
    }
}
