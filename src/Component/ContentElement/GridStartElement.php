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
        // TODO: Rewrite using ScopeMatcher since Contao 4.4. is released

        if (TL_MODE === 'BE') {
            return '';
        }

        return parent::generate();
    }

    /**
     * {@inheritdoc}
     */
    protected function compile()
    {
        $provider = $this->getGridProvider();
        $iterator = $provider->getIterator('ce:' . $this->id, $this->bootstrap_grid);

        $this->Template->rowClasses    = $iterator->row();
        $this->Template->columnClasses = $iterator->current();
    }
}
