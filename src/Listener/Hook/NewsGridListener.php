<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Hook;

use Contao\ModuleNews;
use Contao\Template;
use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\GridProvider;

use function strpos;

final class NewsGridListener
{
    /**
     * Grid provider.
     */
    private GridProvider $gridProvider;

    /**
     * @param GridProvider $gridProvider Grid provider.
     */
    public function __construct(GridProvider $gridProvider)
    {
        $this->gridProvider = $gridProvider;
    }

    /**
     * Parse the news article.
     *
     * @param Template            $template    The template.
     * @param array<string,mixed> $newsArticle The news article.
     * @param ModuleNews          $module      The news module.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function onParseArticles(Template $template, array $newsArticle, ModuleNews $module): void
    {
        if ($module->type !== 'newslist' && $module->type !== 'newsarchive') {
            return;
        }

        if ($module->bs_grid < 1) {
            return;
        }

        try {
            $gridIterator = $this->gridProvider->getIterator('mod:' . $module->id, (int) $module->bs_grid);
        } catch (GridNotFound $e) {
            return;
        }

        $newsTemplate = clone $template;

        $template->setName('bs_grid_wrapper');

        $template->bsGrid = [
            'first'   => $gridIterator->key() === 0,
            'last'    => strpos($template->class, 'last') !== false,
            'row'     => $gridIterator->row(),
            'grid'    => $gridIterator->current(),
            'resets'  => $gridIterator->resets(),
            'content' => static function () use ($newsTemplate, $template): string {
                $newsTemplate->setData($template->getData());

                return $newsTemplate->parse();
            },
        ];

        $gridIterator->next();
    }
}
