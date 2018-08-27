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

namespace ContaoBootstrap\Grid\Component\ContentElement;

use Contao\ContentModel;
use Contao\Database\Result;
use Contao\User;
use ContaoBootstrap\Grid\GridProvider;
use Netzmacht\Contao\Toolkit\Component\Component;
use Netzmacht\Contao\Toolkit\Component\ComponentFactory;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;

/**
 * Class GalleryFactory
 */
final class GalleryFactory implements ComponentFactory
{
    /**
     * Template engine.
     *
     * @var TemplateEngine
     */
    private $templateEngine;

    /**
     * The grid provider.
     *
     * @var GridProvider
     */
    private $gridProvider;

    /**
     * Frontend user.
     *
     * @var User
     */
    private $user;

    /**
     * GalleryFactory constructor.
     *
     * @param TemplateEngine $templateEngine Template engine.
     * @param GridProvider   $gridProvider   The grid provider.
     * @param User           $user           Frontend user.
     */
    public function __construct(TemplateEngine $templateEngine, GridProvider $gridProvider, User $user)
    {
        $this->templateEngine = $templateEngine;
        $this->gridProvider   = $gridProvider;
        $this->user           = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($model): bool
    {
        if (!$model instanceof ContentModel && !($model instanceof Result)) {
            return false;
        }

        return $model->type === 'bs_grid_gallery';
    }

    /**
     * {@inheritdoc}
     */
    public function create($model, string $column): Component
    {
        return new GalleryElement(
            $model,
            $this->templateEngine,
            $this->gridProvider,
            $this->user,
            $column
        );
    }
}
