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

namespace ContaoBootstrap\Grid\Component\FormField;

/**
 * Class GridStopFormField.
 *
 * @package ContaoBootstrap\Grid\Component\FormField
 */
class GridStopFormField extends AbstractRelatedFormField
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $strTemplate = 'form_grid_stop';

    /**
     * {@inheritdoc}
     */
    public function parse($attributes = null)
    {
        $iterator = $this->getIterator();
        if ($iterator) {
            $iterator->rewind();
        }

        if ($this->isBackendRequest()) {
            return $this->renderBackendView($this->getParent());
        }

        return parent::parse($attributes);
    }
}
