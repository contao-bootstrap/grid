<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\FormField;

use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\GridIterator;

class GridStartFormField extends AbstractFormField
{
    /**
     * Template name.
     */
    protected string $strTemplate = 'form_bs_gridStart';

    protected function getIterator(): ?GridIterator
    {
        try {
            $provider = $this->getGridProvider();
            $iterator = $provider->getIterator('ffl:' . $this->id, (int) $this->bs_grid);
            $this->getResponseTagger()->addTags(['contao.db.tl_bs_grid.' . $this->bs_grid]);

            return $iterator;
        } catch (GridNotFound $e) {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attributes = null): string
    {
        $iterator = $this->getIterator();

        if ($this->isBackendRequest()) {
            return $this->renderBackendView($this, $iterator);
        }

        if ($iterator) {
            $this->rowClasses    = $iterator->row();
            $this->columnClasses = $iterator->current();
        }

        return parent::parse($attributes);
    }
}
