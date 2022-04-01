<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\DataContainer;
use Contao\FormFieldModel;
use Contao\Model\Collection;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;

use function time;

/**
 * Class FormFixFormFieldParentRelationsListener fixes the parent relation of grid element for forms
 */
final class FormFixFormFieldParentRelationsListener
{
    /**
     * Repository manager.
     */
    private RepositoryManager $repositoryManager;

    /**
     * @param RepositoryManager $repositoryManager Repository manager.
     */
    public function __construct(RepositoryManager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * Handle the oncopy_callback.
     *
     * @param string|int    $insertId      Id of new created record.
     * @param DataContainer $dataContainer Data container.
     */
    public function onCopy($insertId, DataContainer $dataContainer): void
    {
        $collection = $this->loadFormFieldModels($dataContainer->table, (int) $insertId);
        if ($collection === null) {
            return;
        }

        $activeParent = null;
        foreach ($collection as $model) {
            if ($model->type === 'bs_gridStart') {
                $activeParent = $model;
                continue;
            }

            // Broken configuration
            if ($activeParent === null) {
                continue;
            }

            $this->repositoryManager->getConnection()->update(
                FormFieldModel::getTable(),
                [
                    'bs_grid_parent' => $activeParent->id,
                    'tstamp'         => time(),
                ],
                [
                    'id' => $model->id,
                ]
            );
        }
    }

    /**
     * Load grid form fields which have to be adjusted.
     *
     * @param string $parentTable The parent table.
     * @param int    $parentId    The parent id.
     *
     * @return Collection|FormFieldModel[]|null
     */
    private function loadFormFieldModels(string $parentTable, int $parentId): ?Collection
    {
        $constraints = ['.pid=?', 'FIND_IN_SET( .type, \'bs_gridStart,bs_gridSeparator,bs_gridStop\')'];
        $values      = [$parentId, $parentTable];

        return $this->repositoryManager
            ->getRepository(FormFieldModel::class)
            ->findBy($constraints, $values, ['order' => '.sorting']);
    }
}
