<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoBootstrap\Grid\Component\ContentElement;

use Contao\ContentModel;

/**
 * Class GridStopElement.
 *
 * @package ContaoBootstrap\Grid\Component\ContentElement
 */
class GridStopElement extends AbstractGridElement
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $strTemplate = 'ce_grid_stop';

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        if ($this->isBackendRequest()) {
            return '';
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
     * {@inheritDoc}
     */
    protected function getIterator()
    {
        $provider = $this->getGridProvider();
        $parent   = ContentModel::findByPk($this->bootstrap_grid_parent);

        if ($parent) {
            try {
                return $provider->getIterator('ce:' . $parent->id, $parent->bootstrap_grid);
            } catch (\Exception $e) {}
        }

        return null;
    }

}
