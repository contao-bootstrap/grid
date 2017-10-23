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

use Contao\FormFieldModel;
use ContaoBootstrap\Grid\GridIterator;

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
    protected function getIterator():? GridIterator
    {
        $provider = $this->getGridProvider();
        $parent   = $this->getParent();

        if ($parent) {
            try {
                return $provider->getIterator('ffl:' . $parent->id, (int) $parent->bs_grid);
            } catch (\Exception $e) {
                // Do nothing. Error is displayed in backend view.
            }
        }

        return null;
    }

    /**
     * Get the parent model.
     *
     * @return FormFieldModel|null
     */
    protected function getParent():? FormFieldModel
    {
        return FormFieldModel::findByPk($this->bs_grid_parent);
    }
}
