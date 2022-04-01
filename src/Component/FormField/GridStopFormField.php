<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\FormField;

class GridStopFormField extends AbstractRelatedFormField
{
    /**
     * Template name.
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
    protected $strTemplate = 'form_bs_gridStop';

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
