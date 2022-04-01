<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\FormField;

class GridStopFormField extends AbstractRelatedFormField
{
    /**
     * Template name.
     */
    protected string $strTemplate = 'form_bs_gridStop';

    /**
     * {@inheritdoc}
     */
    public function parse($attributes = null): string
    {
        $iterator = $this->getIterator();
        if ($iterator) {
            $iterator->rewind();
        }

        if ($this->isBackendRequest()) {
            return $this->renderBackendView($this->getParent());
        }

        return parent::parse($attributes);
    }
}
