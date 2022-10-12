<?php

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
     */
    private RepositoryManager $repositoryManager;

    /** @param RepositoryManager $repositoryManager Repository manager. */
    public function __construct(RepositoryManager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * Handle the onsubmit callback to automatically select closest parent id.
     *
     * @param DataContainer $dataContainer Data container driver.
     */
    public function onSubmit(DataContainer $dataContainer): void
    {
        if (! $dataContainer->activeRecord) {
            return;
        }

        if (! in_array($dataContainer->activeRecord->type, ['bs_gridSeparator', 'bs_gridStop'], true)) {
            return;
        }

        if ($dataContainer->activeRecord->bs_grid_parent > 0) {
            return;
        }

        /** @psalm-var Result|ContentModel $dataContainer->activeRecord */

        $this->fixContentElement($dataContainer->activeRecord);
    }

    /**
     * Handle the oncopy callback.
     *
     * @param int|string $elementId Element id of copied element.
     */
    public function onCopy(int|string $elementId): void
    {
        $contentModel = $this->repositoryManager->getRepository(ContentModel::class)->find((int) $elementId);
        if (! $contentModel instanceof ContentModel) {
            return;
        }

        $this->fixContentElement($contentModel);
    }

    /**
     * Fix grid start relation of content element.
     *
     * @param ContentModel|Result $contentModel Content element.
     */
    private function fixContentElement(ContentModel|Result $contentModel): void
    {
        if (! in_array($contentModel->type, ['bs_gridSeparator', 'bs_gridStop'], true)) {
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
                'tstamp'         => time(),
            ],
            [
                'id' => $contentModel->id,
            ],
        );
    }

    /**
     * Load closest grid start model.
     *
     * @param ContentModel|Result $contentModel Content model.
     *
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    private function loadClosestGridStartModel(ContentModel|Result $contentModel): ContentModel|null
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
