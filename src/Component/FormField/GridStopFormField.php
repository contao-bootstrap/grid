<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\FormField;

use Override;

/** @psalm-suppress PropertyNotSetInConstructor - Issue in the codebase of Contao */
final class GridStopFormField extends AbstractRelatedFormField
{
    /**
     * Template name.
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
    protected $strTemplate = 'form_bs_gridStop';

    /**
     * {@inheritDoc}
     */
    #[Override]
    public function parse($arrAttributes = null): string
    {
        $iterator = $this->getIterator();
        $iterator?->rewind();

        if ($this->isBackendRequest()) {
            return $this->renderBackendView($this->getParent());
        }

        return parent::parse($arrAttributes);
    }
}
