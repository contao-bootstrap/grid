<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\Module;

use Contao\Database\Result;
use Contao\ModuleModel;
use ContaoBootstrap\Grid\GridProvider;
use Netzmacht\Contao\Toolkit\Component\Component;
use Netzmacht\Contao\Toolkit\Component\ComponentFactory;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

final class GridModuleFactory implements ComponentFactory
{
    /**
     * Template engine.
     */
    private TemplateEngine $templateEngine;

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
     * @param TemplateEngine $templateEngine Template engine.
     * @param Translator     $translator     Translator.
     * @param GridProvider   $gridProvider   Grid provider.
     * @param ResponseTagger $responseTagger Response tagger.
     */
    public function __construct(
        TemplateEngine $templateEngine,
        Translator $translator,
        GridProvider $gridProvider,
        ResponseTagger $responseTagger
    ) {
        $this->templateEngine = $templateEngine;
        $this->translator     = $translator;
        $this->gridProvider   = $gridProvider;
        $this->responseTagger = $responseTagger;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($model): bool
    {
        if (! $model instanceof ModuleModel && ! ($model instanceof Result)) {
            return false;
        }

        return $model->type === 'bs_grid';
    }

    /**
     * {@inheritdoc}
     */
    public function create($model, string $column): Component
    {
        return new GridModule(
            $model,
            $this->templateEngine,
            $this->translator,
            $this->gridProvider,
            $this->responseTagger,
            $column
        );
    }
}
