<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\ContentElement;

use Contao\ContentModel;
use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\GridIterator;

final class GridStopElement extends AbstractGridElement
{
    /**
     * Template name.
     */
    protected string $templateName = 'ce_bs_gridStop';

    public function generate(): string
    {
        if ($this->isBackendRequest()) {
            return $this->renderBackendView($this->getParent());
        }

        return parent::generate();
    }

    /**
     * {@inheritdoc}
     */
    protected function compile()
    {
        $iterator = $this->getIterator();

        if (! $iterator) {
            return;
        }

        $iterator->rewind();
    }

    /**
     * Get the parent model.
     */
    protected function getParent(): ?ContentModel
    {
        return ContentModel::findByPk($this->get('bs_grid_parent'));
    }

    protected function getIterator(): ?GridIterator
    {
        $provider = $this->getGridProvider();
        $parent   = $this->getParent();

        if ($parent) {
            try {
                $iterator = $provider->getIterator('ce:' . $parent->id, (int) $parent->bs_grid);
                $this->responseTagger->addTags(['contao.db.tl_bs_grid.' . $parent->bs_grid]);

                return $iterator;
            } catch (GridNotFound $e) {
                // Do nothing. In backend view an error is shown anyway.
                return null;
            }
        }

        return null;
    }
}
