<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\ContentElement;

use Contao\ContentModel;
use ContaoBootstrap\Grid\GridIterator;

/**
 * Class GridSeparatorElement.
 *
 * @package ContaoBootstrap\Grid\Component\ContentElement
 */
final class GridSeparatorElement extends AbstractGridElement
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $strTemplate = 'ce_grid_separator';

    /**
     * {@inheritdoc}
     */
    public function generate()
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
    protected function compile()
    {
        $iterator = $this->getIterator();

        if ($iterator) {
            $iterator->next();

            $this->Template->columnClasses = $iterator->current();
            $this->Template->resets        = $iterator->resets();
        } else {
            $this->Template->resets = [];
        }
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
                return $provider->getIterator('ce:' . $parent->id, (int) $parent->bs_grid);
            } catch (\Exception $e) {
                // Do nothing. In backend view an error is shown anyway.
            }
        }

        return null;
    }

    /**
     * Get the parent model.
     *
     * @return ContentModel|null
     */
    protected function getParent():? ContentModel
    {
        return ContentModel::findByPk($this->bs_grid_parent);
    }
}
