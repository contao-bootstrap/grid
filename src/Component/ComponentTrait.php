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

namespace ContaoBootstrap\Grid\Component;

use Contao\BackendTemplate;
use Contao\Model;
use Contao\System;
use ContaoBootstrap\Grid\GridIterator;
use ContaoBootstrap\Grid\GridProvider;
use ContaoBootstrap\Grid\View\ComponentRenderHelper;

/**
 * Trait ComponentTrait.
 *
 * @deprecated
 */
trait ComponentTrait
{
    /**
     * Get hte component render helper.
     *
     * @return ComponentRenderHelper
     */
    private function getHelper(): ComponentRenderHelper
    {
        return System::getContainer()->get('contao_bootstrap.grid.view.renderer_helper');
    }

    /**
     * Get the grid provider.
     *
     * @return GridProvider
     */
    protected function getGridProvider(): GridProvider
    {
        return $this->getHelper()->getGridProvider();
    }

    /**
     * Render the backend view.
     *
     * @param Model|null   $start    Start element.
     * @param GridIterator $iterator Iterator.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function renderBackendView($start, GridIterator $iterator = null): string
    {
        $template = new BackendTemplate('be_bs_grid');

        if ($start) {
            $template->name  = $start->bs_grid_name;
            $template->color = $this->getHelper()->rotateColor('ce:' . $start->id);
        }

        if (!$start) {
            $template->error = $GLOBALS['TL_LANG']['ERR']['bsGridParentMissing'];
        }

        if ($iterator) {
            $template->classes = $iterator->current();
        }

        return $template->parse();
    }

    /**
     * Check if we are in backend mode.
     *
     * @return bool
     */
    protected function isBackendRequest(): bool
    {
        return $this->getHelper()->isBackendRequest();
    }

    /**
     * Get the iterator.
     *
     * @return GridIterator
     */
    abstract protected function getIterator(): ?GridIterator;
}
