<?php

/**
 * Contao Bootstrap grid.
 *
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid\View;

use ContaoBootstrap\Core\Helper\ColorRotate;
use ContaoBootstrap\Grid\GridProvider;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;

/** @deprecated */
final class ComponentRenderHelper
{
    /**
     * Grid provider.
     */
    private GridProvider $gridProvider;

    /**
     * Request scope matcher.
     */
    private RequestScopeMatcher $scopeMatcher;

    /**
     * Color rotate helper.
     */
    private ColorRotate $colorRotate;

    /**
     * @param GridProvider        $gridProvider The grid provider.
     * @param RequestScopeMatcher $scopeMatcher The request scope matcher.
     * @param ColorRotate         $colorRotate  The color rotate.
     */
    public function __construct(GridProvider $gridProvider, RequestScopeMatcher $scopeMatcher, ColorRotate $colorRotate)
    {
        $this->gridProvider = $gridProvider;
        $this->scopeMatcher = $scopeMatcher;
        $this->colorRotate  = $colorRotate;
    }

    /**
     * Get gridProvider.
     */
    public function getGridProvider(): GridProvider
    {
        return $this->gridProvider;
    }

    /**
     * Check if current request is a backend request.
     */
    public function isBackendRequest(): bool
    {
        return $this->scopeMatcher->isBackendRequest();
    }

    /**
     * Rotate the color for an identifier.
     *
     * @param string $identifier The color identifier.
     */
    public function rotateColor(string $identifier): string
    {
        return $this->colorRotate->getColor($identifier);
    }
}
