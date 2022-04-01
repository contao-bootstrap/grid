<?php

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
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

final class GridElementFactory
{
    /**
     * Panel element types.
     *
     * @var array<string,string>
     */
    private array $gridElementTypes = [
        'bs_gridStart'     => GridStartElement::class,
        'bs_gridSeparator' => GridSeparatorElement::class,
        'bs_gridStop'      => GridStopElement::class,
    ];

    /**
     * Template engine.
     */
    private TemplateEngine $templateEngine;

    /**
     * Color rotate.
     */
    private ColorRotate $colorRotate;

    /**
     * Request scope matcher.
     */
    private RequestScopeMatcher $scopeMatcher;

    /**
     * Translator.
     */
    private Translator $translator;

    /**
     * Grid provider.
     */
    private GridProvider $gridProvider;

    /**
     * Response tagger.
     */
    private ResponseTagger $responseTagger;

    /**
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
        if (! $model instanceof ContentModel && ! ($model instanceof Result)) {
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
        if (! isset($this->gridElementTypes[$model->type])) {
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
