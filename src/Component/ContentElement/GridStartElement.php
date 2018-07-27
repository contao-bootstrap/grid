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
    protected $strTemplate = 'ce_bs_gridStart';

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        if ($this->isBackendRequest()) {
            $iterator = $this->getIterator();

            return $this->renderBackendView($this->objModel, $iterator);
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
            $this->Template->rowClasses    = $iterator->row();
            $this->Template->columnClasses = $iterator->current();
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getIterator():? GridIterator
    {
        try {
            $provider = $this->getGridProvider();
            $iterator = $provider->getIterator('ce:' . $this->id, (int) $this->bs_grid);

            return $iterator;
        } catch (\Exception $e) {
            // Do nothing. In backend view an error is shown anyway.
        }

        return null;
    }
}
