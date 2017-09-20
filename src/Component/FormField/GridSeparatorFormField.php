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

namespace ContaoBootstrap\Grid\Component\FormField;

/**
 * Class GridSeparatorFormField.
 *
 * @package ContaoBootstrap\Grid\Component\FormField
 */
class GridSeparatorFormField extends AbstractRelatedFormField
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $strTemplate = 'form_grid_separator';

    /**
     * {@inheritdoc}
     */
    public function parse($attributes = null): string
    {
        $iterator = $this->getIterator();
        if ($iterator) {
            $iterator->next();
        }

        if ($this->isBackendRequest()) {
            return $this->renderBackendView($this->getParent(), $iterator);
        }

        if ($iterator) {
            $this->rowClasses    = $iterator->row();
            $this->columnClasses = $iterator->current();
            $this->resets        = $iterator->resets();
        } else {
            $this->resets = [];
        }

        return parent::parse($attributes);
    }
}
