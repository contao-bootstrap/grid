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

class GridSeparatorFormField extends AbstractFormField
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $strTemplate = 'form_grid_separator';

    /**
     * @inheritDoc
     */
    public function generate()
    {
        // TODO: Implement generate() method.
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attributes = null)
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

    /**
     * @inheritDoc
     */
    protected function getIterator()
    {
        $provider = $this->getGridProvider();
        $parent   = $this->getParent();

        if ($parent) {
            try {
                return $provider->getIterator('ffl:' . $parent->id, $parent->bootstrap_grid);
            } catch (\Exception $e) {}
        }
    }

    /**
     * Get the parent model.
     *
     * @return FormFieldModel|null
     */
    protected function getParent()
    {
        return FormFieldModel::findByPk($this->bootstrap_grid_parent);
    }
}
