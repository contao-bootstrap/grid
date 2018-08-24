<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2018 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid\View;

use ContaoBootstrap\Core\Helper\ColorRotate;
use ContaoBootstrap\Grid\GridProvider;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;

/**
 * Class ComponentRenderHelper
 *
 * @deprecated
 */
final class ComponentRenderHelper
{
    /**
     * Grid provider.
     *
     * @var GridProvider
     */
    private $gridProvider;

    /**
     * Request scope matcher.
     *
     * @var RequestScopeMatcher
     */
    private $scopeMatcher;

    /**
     * Color rotate helper.
     *
     * @var ColorRotate
     */
    private $colorRotate;

    /**
     * ComponentRenderHelper constructor.
     *
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
     *
     * @return GridProvider
     */
    public function getGridProvider(): GridProvider
    {
        return $this->gridProvider;
    }

    /**
     * Check if current request is a backend request.
     *
     * @return bool
     */
    public function isBackendRequest(): bool
    {
        return $this->scopeMatcher->isBackendRequest();
    }

    /**
     * Rotate the color for an identifier.
     *
     * @param string $identifier The color identifier.
     *
     * @return string
     */
    public function rotateColor(string $identifier): string
    {
        return $this->colorRotate->getColor($identifier);
    }
}
