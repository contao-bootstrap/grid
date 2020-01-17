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

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\ContentModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use function in_array;
use function time;

/**
 * Class ContentFixParentRelationListener fixes the parent relation if a content element is copied
 */
final class ContentFixParentRelationListener
{
    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * ContentFixParentRelationListener constructor.
     *
     * @param RepositoryManager $repositoryManager Repository manager.
     */
    public function __construct(RepositoryManager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * Handle the oncopy callback.
     *
     * @param int|string $elementId Element id of copied element.
     *
     * @return void
     */
    public function onCopy($elementId): void
    {
        /** @var ContentModel|null $contentModel */
        $elementId    = (int) $elementId;
        $contentModel = $this->repositoryManager->getRepository(ContentModel::class)->find($elementId);
        if ($contentModel === null || !in_array($contentModel->type, ['bs_gridSeparator', 'bs_gridStop'], true)) {
            return;
        }

        $parentModel = $this->loadParentModel($contentModel);
        if ($parentModel === null) {
            return;
        }

        $this->repositoryManager->getConnection()->update(
            ContentModel::getTable(),
            [
                'bs_grid_parent' => $parentModel->id,
                'tstamp'         => time()
            ],
            [
                'id' => $contentModel->id
            ]
        );
    }

    private function loadParentModel(ContentModel $contentModel) : ?ContentModel
    {
        $constraints = ['.pid=?', '.type=?', '.sorting < ?'];
        $values      = [$contentModel->pid, 'bs_gridStart', $contentModel->sorting];

        if ($contentModel->ptable === 'tl_article' || $contentModel->ptable === '') {
            $constraints[] = '( .ptable=? OR .ptable=?)';
            $values[]      = '';
            $values[]      = 'tl_article';
        } else {
            $constraints[] = '.ptable=?';
            $values[]      = $contentModel->ptable;
        }

        return $this->repositoryManager
            ->getRepository(ContentModel::class)
            ->findOneBy($constraints, $values, ['order' => '.sorting DESC']);
    }
}
