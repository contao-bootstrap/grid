<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoBootstrap\Grid\Component\ContentElement;

use Contao\ContentElement;
use Contao\ContentModel;
use ContaoBootstrap\Grid\GridProvider;


/**
 * Class GridStopElement.
 *
 * @package ContaoBootstrap\Grid\Component\ContentElement
 */
class GridStopElement extends ContentElement
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
            $this->getGridProvider()
                ->getIterator('ce:' . $parent->id, $parent->bootstrap_grid)
                ->rewind();
        }
    }

    /**
     * @return GridProvider
     */
    protected function getGridProvider()
    {
        return static::getContainer()->get('contao_bootstrap.grid.grid_provider');
    }
}
