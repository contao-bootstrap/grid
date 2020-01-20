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
use Contao\Database\Result;
use Contao\DataContainer;
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
     * Handle the onsubmit callback to automatically select closest parent id.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return void
     */
    public function onSubmit(DataContainer $dataContainer): void
    {
        if (! in_array($dataContainer->activeRecord->type, ['bs_gridSeparator', 'bs_gridStop'], true)) {
            return;
        }

        if ($dataContainer->activeRecord->bs_grid_parent > 0) {
            return;
        }

        $this->fixContentElement($dataContainer->activeRecord);
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
        $contentModel = $this->repositoryManager->getRepository(ContentModel::class)->find((int) $elementId);
        if ($contentModel === null) {
            return;
        }

        $this->fixContentElement($contentModel);
    }

    /**
     * Fix grid start relation of content element.
     *
     * @param ContentModel|Result $contentModel Content element.
     *
     * @return void
     *
     * @throws \Doctrine\DBAL\DBALException When a database error occurs.
     */
    private function fixContentElement($contentModel): void
    {
        if (!in_array($contentModel->type, ['bs_gridSeparator', 'bs_gridStop'], true)) {
            return;
        }

        $parentModel = $this->loadClosestGridStartModel($contentModel);
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

    /**
     * Load closest grid start model.
     *
     * @param ContentModel|Result $contentModel Content model.
     *
     * @return ContentModel|null
     */
    private function loadClosestGridStartModel($contentModel) : ?ContentModel
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
