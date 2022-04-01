<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\ContentElement;

use Contao\ContentModel;
use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\GridIterator;

final class GridSeparatorElement extends AbstractGridElement
{
    /**
     * Template name.
     */
    protected string $templateName = 'ce_bs_gridSeparator';

    public function generate(): string
    {
        if ($this->isBackendRequest()) {
            $iterator = $this->getIterator();

            if ($iterator) {
                $iterator->next();
            }

            return $this->renderBackendView($this->getParent(), $iterator);
        }

        return parent::generate();
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareTemplateData(array $data): array
    {
        $iterator = $this->getIterator();
        $data     = parent::prepareTemplateData($data);

        if ($iterator) {
            $iterator->next();

            $data['columnClasses'] = $iterator->current();
            $data['resets']        = $iterator->resets();
        } else {
            $data['resets'] = [];
        }

        return $data;
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

    /**
     * Get the parent model.
     */
    protected function getParent(): ?ContentModel
    {
        return ContentModel::findByPk($this->get('bs_grid_parent'));
    }
}
