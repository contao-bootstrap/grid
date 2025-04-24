<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\FormFieldModel;
use Contao\Model\Collection;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;

use function time;

/**
 * Class FormFixFormFieldParentRelationsListener fixes the parent relation of grid element for forms
 */
final class FormFixFormFieldParentRelationsListener
{
    /** @param RepositoryManager $repositoryManager Repository manager. */
    public function __construct(private readonly RepositoryManager $repositoryManager)
    {
    }

    /**
     * Handle the oncopy_callback.
     *
     * @param string|int $insertId Id of new created record.
     */
    public function onCopy(string|int $insertId): void
    {
        $collection = $this->loadFormFieldModels((int) $insertId);
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
                ],
            );
        }
    }

    /**
     * Load grid form fields which have to be adjusted.
     *
     * @param int $parentId The parent id.
     *
     * @return Collection|FormFieldModel[]|null
     * @psalm-return Collection|null
     */
    private function loadFormFieldModels(int $parentId): Collection|null
    {
        $constraints = ['.pid=?', 'FIND_IN_SET( .type, \'bs_gridStart,bs_gridSeparator,bs_gridStop\')'];
        $values      = [$parentId];

        return $this->repositoryManager
            ->getRepository(FormFieldModel::class)
            ->findBy($constraints, $values, ['order' => '.sorting']);
    }
}
