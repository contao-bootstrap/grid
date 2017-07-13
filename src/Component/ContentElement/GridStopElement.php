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
        // TODO: Rewrite using ScopeMatcher since Contao 4.4. is released

        if (TL_MODE === 'BE') {
            return '<hr style="background: red;">';
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
}
