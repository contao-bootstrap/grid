<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\ContentElement;

use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\GridIterator;

final class GridStartElement extends AbstractGridElement
{
    /**
     * Template name.
     */
    protected string $templateName = 'ce_bs_gridStart';

    public function generate(): string
    {
        if ($this->isBackendRequest()) {
            $iterator = $this->getIterator();

            return $this->renderBackendView($this->getModel(), $iterator);
        }

        return parent::generate();
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareTemplateData(array $data): array
    {
        $data = parent::prepareTemplateData($data);

        $iterator = $this->getIterator();
        if ($iterator) {
            $data['rowClasses']    = $iterator->row();
            $data['columnClasses'] = $iterator->current();
        }

        return $data;
    }

    protected function getIterator(): ?GridIterator
    {
        try {
            $provider = $this->getGridProvider();
            $iterator = $provider->getIterator('ce:' . $this->get('id'), (int) $this->get('bs_grid'));
            $this->responseTagger->addTags(['contao.db.tl_bs_grid.' . $this->get('bs_grid')]);

            return $iterator;
        } catch (GridNotFound $e) {
            // Do nothing. In backend view an error is shown anyway.
            return null;
        }
    }
}
