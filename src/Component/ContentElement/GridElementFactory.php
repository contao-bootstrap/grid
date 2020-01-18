<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2020 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\ContentElement;

use Contao\ContentModel;
use Contao\Database\Result;
use ContaoBootstrap\Core\Helper\ColorRotate;
use ContaoBootstrap\Grid\GridProvider;
use Netzmacht\Contao\Toolkit\Component\Component;
use Netzmacht\Contao\Toolkit\Component\Exception\ComponentNotFound;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class GridElementFactory
 *
 * @package ContaoBootstrap\Grid\Component\ContentElement
 */
final class GridElementFactory
{
    /**
     * Panel element types.
     *
     * @var array
     */
    private $gridElementTypes = [
        'bs_gridStart'     => GridStartElement::class,
        'bs_gridSeparator' => GridSeparatorElement::class,
        'bs_gridStop'      => GridStopElement::class,
    ];

    /**
     * Template engine.
     *
     * @var TemplateEngine
     */
    private $templateEngine;

    /**
     * Color rotate.
     *
     * @var ColorRotate
     */
    private $colorRotate;

    /**
     * Request scope matcher.
     *
     * @var RequestScopeMatcher
     */
    private $scopeMatcher;

    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * Grid provider.
     *
     * @var GridProvider
     */
    private $gridProvider;

    /**
     * Response tagger.
     *
     * @var ResponseTagger
     */
    private $responseTagger;

    /**
     * PanelElementFactory constructor.
     *
     * @param TemplateEngine      $templateEngine The template engine.
     * @param Translator          $translator     Translator.
     * @param RequestScopeMatcher $scopeMatcher   Request scope matcher.
     * @param GridProvider        $gridProvider   Grid provider.
     * @param ColorRotate         $colorRotate    Color rotate helper.
     * @param ResponseTagger      $responseTagger Response tagger.
     */
    public function __construct(
        TemplateEngine $templateEngine,
        Translator $translator,
        RequestScopeMatcher $scopeMatcher,
        GridProvider $gridProvider,
        ColorRotate $colorRotate,
        ResponseTagger $responseTagger
    ) {
        $this->templateEngine = $templateEngine;
        $this->colorRotate    = $colorRotate;
        $this->scopeMatcher   = $scopeMatcher;
        $this->translator     = $translator;
        $this->gridProvider   = $gridProvider;
        $this->responseTagger = $responseTagger;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($model): bool
    {
        if (!$model instanceof ContentModel && !($model instanceof Result)) {
            return false;
        }

        return isset($this->gridElementTypes[$model->type]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ComponentNotFound If an unsupported element type is given.
     */
    public function create($model, string $column): Component
    {
        if (!isset($this->gridElementTypes[$model->type])) {
            throw ComponentNotFound::forModel($model);
        }

        $className = $this->gridElementTypes[$model->type];

        return new $className(
            $model,
            $this->templateEngine,
            $this->translator,
            $this->gridProvider,
            $this->scopeMatcher,
            $this->colorRotate,
            $this->responseTagger,
            $column
        );
    }
}
