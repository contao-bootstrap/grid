<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Hook;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\FrontendTemplate;
use Contao\Template;
use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\GridProvider;

use function count;
use function is_array;
use function str_starts_with;

final class NewsGridListener
{
    /**
     * Grid provider.
     */
    private GridProvider $gridProvider;

    /** @param GridProvider $gridProvider Grid provider. */
    public function __construct(GridProvider $gridProvider)
    {
        $this->gridProvider = $gridProvider;
    }

    /** @Hook("parseTemplate") */
    public function onParseTemplate(Template $template): void
    {
        if (! str_starts_with($template->getName(), 'mod_news') || ! is_array($template->articles)) {
            return;
        }

        if ($template->bs_grid < 1) {
            return;
        }

        try {
            $gridIterator = $this->gridProvider->getIterator('mod:' . $template->id, (int) $template->bs_grid);
        } catch (GridNotFound) {
            return;
        }

        $articles = [];

        foreach ($template->articles as $index => $article) {
            $wrapperTemplate = new FrontendTemplate('bs_grid_wrapper');

            /**
             * @psalm-suppress UndefinedMagicPropertyAssignment
             * @psalm-suppress InvalidOperand
             */
            $wrapperTemplate->bsGrid = [
                'first'   => $index === 0,
                'last'    => count($template->articles) - $index === 1,
                'row'     => $gridIterator->row(),
                'grid'    => $gridIterator->current(),
                'resets'  => $gridIterator->resets(),
                'content' => static fn (): string => $article,
            ];

            $articles[] = $wrapperTemplate->parse();

            $gridIterator->next();
        }

        $template->articles = $articles;
    }
}
