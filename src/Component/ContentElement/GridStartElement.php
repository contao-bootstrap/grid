<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2019 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\ContentElement;

use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\GridIterator;

/**
 * Class GridStartElement.
 *
 * @package ContaoBootstrap\Grid\Component\ContentElement
 */
final class GridStartElement extends AbstractGridElement
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $templateName = 'ce_bs_gridStart';

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritDoc}
     */
    protected function getIterator():? GridIterator
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
