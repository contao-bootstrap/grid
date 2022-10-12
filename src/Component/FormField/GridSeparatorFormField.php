<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\FormField;

/**
 * @property int|string        $bs_grid
 * @property string            $rowClasses
 * @property string            $columnClasses
 * @property list<string>|null $resets
 * @psalm-suppress PropertyNotSetInConstructor - Issue in the codebase of Contao
 */
final class GridSeparatorFormField extends AbstractRelatedFormField
{
    /**
     * Template name.
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
    protected $strTemplate = 'form_bs_gridSeparator';

    /**
     * {@inheritdoc}
     */
    public function parse($arrAttributes = null): string
    {
        $iterator = $this->getIterator();
        $iterator?->next();

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

        return parent::parse($arrAttributes);
    }
}
