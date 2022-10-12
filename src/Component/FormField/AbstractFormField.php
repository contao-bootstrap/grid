<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\FormField;

use Contao\BackendTemplate;
use Contao\Model;
use Contao\System;
use Contao\Widget;
use ContaoBootstrap\Grid\GridIterator;
use ContaoBootstrap\Grid\GridProvider;
use ContaoBootstrap\Grid\View\ComponentRenderHelper;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;

/** @psalm-suppress DeprecatedTrait */
abstract class AbstractFormField extends Widget
{
    /**
     * Get the component render helper.
     *
     * @psalm-suppress DeprecatedClass
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress InvalidNullableReturnType
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress NullableReturnStatement
     */
    private function getHelper(): ComponentRenderHelper
    {
        return System::getContainer()->get('contao_bootstrap.grid.view.renderer_helper');
    }

    /**
     * Get the grid provider.
     */
    protected function getGridProvider(): GridProvider
    {
        return $this->getHelper()->getGridProvider();
    }

    /**
     * Render the backend view.
     *
     * @param Model|object|null $start    Start element.
     * @param GridIterator      $iterator Iterator.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function renderBackendView(Model|object|null $start, GridIterator|null $iterator = null): string
    {
        $template = new BackendTemplate('be_bs_grid');

        if ($start) {
            $template->name  = $start->bs_grid_name;
            $template->color = $this->getHelper()->rotateColor('ce:' . $start->id);
        }

        if (! $start) {
            $template->error = $GLOBALS['TL_LANG']['ERR']['bsGridParentMissing'];
        }

        if ($iterator) {
            $template->classes = $iterator->current();
        }

        return $template->parse();
    }

    /**
     * Check if we are in backend mode.
     */
    protected function isBackendRequest(): bool
    {
        return $this->getHelper()->isBackendRequest();
    }

    /**
     * Get the iterator.
     */
    abstract protected function getIterator(): GridIterator|null;

    /**
     * Get the response tagger service.
     *
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress InvalidNullableReturnType
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress NullableReturnStatement
     */
    protected function getResponseTagger(): ResponseTagger
    {
        return self::getContainer()->get('contao_bootstrap.grid.response_tagger');
    }
}
