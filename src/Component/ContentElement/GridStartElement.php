<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoBootstrap\Grid\Component\ContentElement;

/**
 * Class GridStartElement.
 *
 * @package ContaoBootstrap\Grid\Component\ContentElement
 */
class GridStartElement extends AbstractGridElement
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $strTemplate = 'ce_grid_start';

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
    protected function getIterator()
    {
        try {
            $provider = $this->getGridProvider();
            $iterator = $provider->getIterator('ce:' . $this->id, $this->bs_grid);

            return $iterator;
        } catch (\Exception $e) {
            // Do nothing. In backend view an error is shown anyway.
        }
    }
}
