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

namespace ContaoBootstrap\Grid\Component\ContentElement;

use Contao\Database\Result;
use Contao\Model;
use ContaoBootstrap\Core\Helper\ColorRotate;
use ContaoBootstrap\Grid\GridIterator;
use ContaoBootstrap\Grid\GridProvider;
use Netzmacht\Contao\Toolkit\Component\ContentElement\AbstractContentElement;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateReference as ToolkitTemplateReference;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class AbstractGridElement.
 *
 * @package ContaoBootstrap\Grid\Component\ContentElement
 */
abstract class AbstractGridElement extends AbstractContentElement
{
    /**
     * Request scope matcher.
     *
     * @var RequestScopeMatcher
     */
    private $scopeMatcher;

    /**
     * Color rotate.
     *
     * @var ColorRotate
     */
    private $colorRotate;

    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * The grid provider.
     *
     * @var GridProvider
     */
    private $gridProvider;

    /**
     * AbstractGridElement constructor.
     *
     * @param Model|Result        $model          Object model or result.
     * @param TemplateEngine      $templateEngine Template engine.
     * @param Translator          $translator     Translator.
     * @param GridProvider        $gridProvider   Grid provider.
     * @param RequestScopeMatcher $scopeMatcher   Request scope matcher.
     * @param ColorRotate         $colorRotate    Color rotate helper.
     * @param string              $column         Column.
     */
    public function __construct(
        $model,
        TemplateEngine $templateEngine,
        Translator $translator,
        GridProvider $gridProvider,
        RequestScopeMatcher $scopeMatcher,
        ColorRotate $colorRotate,
        string $column = 'main'
    ) {
        parent::__construct($model, $templateEngine, $column);

        $this->translator   = $translator;
        $this->gridProvider = $gridProvider;
        $this->colorRotate  = $colorRotate;
        $this->scopeMatcher = $scopeMatcher;
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
        return $this->render(
            new ToolkitTemplateReference(
                'be_bs_grid',
                'html5',
                ToolkitTemplateReference::SCOPE_BACKEND
            ),
            [
                'color'   => $start ? $this->rotateColor('ce:' . $start->id) : null,
                'name'    => $start ? $start->bs_grid_name : null,
                'error'   => !$start ? $this->translator->trans('ERR.bsGridParentMissing', [], 'contao_default') : null,
                'classes' => $iterator ? $iterator->current() : null,
            ]
        );
    }

    /**
     * Check if we are in backend mode.
     *
     * @return bool
     */
    protected function isBackendRequest(): bool
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
    protected function rotateColor(string $identifier): string
    {
        return $this->colorRotate->getColor($identifier);
    }

    /**
     * Get the grid provider.
     *
     * @return GridProvider
     */
    protected function getGridProvider(): GridProvider
    {
        return $this->gridProvider;
    }
}
