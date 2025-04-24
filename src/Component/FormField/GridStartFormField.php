<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\FormField;

use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\GridIterator;

/**
 * @property int|string $bs_grid
 * @property string     $rowClasses
 * @property string     $columnClasses
 * @psalm-suppress PropertyNotSetInConstructor - Issue in the codebase of Contao
 */
final class GridStartFormField extends AbstractFormField
{
    /**
     * Template name.
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
    protected $strTemplate = 'form_bs_gridStart';

    protected function getIterator(): GridIterator|null
    {
        try {
            $provider = $this->getGridProvider();
            $iterator = $provider->getIterator('ffl:' . $this->id, (int) $this->bs_grid);
            $this->getResponseTagger()->addTags(['contao.db.tl_bs_grid.' . $this->bs_grid]);

            return $iterator;
        } catch (GridNotFound) {
            return null;
        }
    }

    public function generate(): string
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function parse($arrAttributes = null): string
    {
        $iterator = $this->getIterator();

        if ($this->isBackendRequest()) {
            return $this->renderBackendView($this, $iterator);
        }

        if ($iterator) {
            $this->rowClasses    = $iterator->row();
            $this->columnClasses = $iterator->current();
        }

        return parent::parse($arrAttributes);
    }
}
