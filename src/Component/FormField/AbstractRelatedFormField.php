<?php

/**
 * @package    Website
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoBootstrap\Grid\Component\FormField;

use Contao\FormFieldModel;

/**
 * Class AbstractRelatedFormField.
 *
 * @package ContaoBootstrap\Grid\Component\FormField
 */
abstract class AbstractRelatedFormField extends AbstractFormField
{
    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        return '';
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
                return $provider->getIterator('ffl:' . $parent->id, $parent->bs_grid);
            } catch (\Exception $e) {
                // Do nothing. Error is displayed in backend view.
            }
        }
    }

    /**
     * Get the parent model.
     *
     * @return FormFieldModel|null
     */
    protected function getParent()
    {
        return FormFieldModel::findByPk($this->bs_grid_parent);
    }
}
