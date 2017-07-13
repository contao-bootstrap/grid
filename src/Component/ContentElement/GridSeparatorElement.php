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
 * Class GridSeparatorElement.
 *
 * @package ContaoBootstrap\Grid\Component\ContentElement
 */
class GridSeparatorElement extends AbstractGridElement
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
        // TODO: Rewrite using ScopeMatcher since Contao 4.4. is released

        if (TL_MODE === 'BE') {
            $iterator = $this->getIterator();

            if ($iterator) {
                $iterator->next();

                return $this->renderBackendView($this->getParent(), $iterator);
            }
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
    protected function getIterator()
    {
        $provider = $this->getGridProvider();
        $parent   = $this->getParent();

        if ($parent) {
            try {
                return $provider->getIterator('ce:' . $parent->id, $parent->bootstrap_grid);
            } catch (\Exception $e) {}
        }

        return null;
    }

    /**
     * Get the parent model.
     *
     * @return ContentModel|null
     */
    protected function getParent()
    {
        return ContentModel::findByPk($this->bootstrap_grid_parent);
    }
}
