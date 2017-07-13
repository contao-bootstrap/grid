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
            return '';
        }

        return parent::generate();
    }

    /**
     * {@inheritdoc}
     */
    protected function compile()
    {
        $parent = ContentModel::findByPk($this->bootstrap_grid_parent);

        if ($parent) {
            $provider = $this->getGridProvider();
            $iterator = $provider->getIterator('ce:' . $parent->id, $parent->bootstrap_grid);

            $iterator->next();

            $this->Template->columnClasses = $iterator->current();
        }
    }
}
