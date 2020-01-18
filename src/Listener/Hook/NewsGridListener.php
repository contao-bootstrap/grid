<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2019 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Hook;

use Contao\Template;
use Contao\ModuleNews;
use ContaoBootstrap\Grid\GridProvider;
use RuntimeException;
use function strpos;

/**
 * Class NewsGridListener
 */
final class NewsGridListener
{
    /**
     * Grid provider.
     *
     * @var GridProvider
     */
    private $gridProvider;

    /**
     * NewsGridListener constructor.
     *
     * @param GridProvider $gridProvider Grid provider.
     */
    public function __construct(GridProvider $gridProvider)
    {
        $this->gridProvider = $gridProvider;
    }

    /**
     * Parse the news article.
     *
     * @param Template   $template    The template.
     * @param array      $newsArticle The news article.
     * @param ModuleNews $module      The news module.
     *
     * @return void
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
        } catch (RuntimeException $e) {
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
            'content' => static function () use ($newsTemplate, $template) {
                $newsTemplate->setData($template->getData());

                return $newsTemplate->parse();
            },
        ];

        $gridIterator->next();
    }
}
