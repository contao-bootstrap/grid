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

use Contao\ContentModel;
use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\GridIterator;

/**
 * Class GridStopElement.
 *
 * @package ContaoBootstrap\Grid\Component\ContentElement
 */
final class GridStopElement extends AbstractGridElement
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $templateName = 'ce_bs_gridStop';

    /**
     * {@inheritdoc}
     */
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

        if ($iterator) {
            $iterator->rewind();
        }
    }

    /**
     * Get the parent model.
     *
     * @return ContentModel|null
     */
    protected function getParent():? ContentModel
    {
        return ContentModel::findByPk($this->get('bs_grid_parent'));
    }

    /**
     * {@inheritDoc}
     */
    protected function getIterator():? GridIterator
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
