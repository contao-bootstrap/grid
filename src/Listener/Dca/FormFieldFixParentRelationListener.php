<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\Database\Result;
use Contao\DataContainer;
use Contao\FormFieldModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;

use function in_array;
use function time;

/**
 * Class FormFieldFixParentRelationListener fixes the parent relation if a form element is copied
 */
final class FormFieldFixParentRelationListener
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
        if (
            ! $dataContainer->activeRecord instanceof FormFieldModel
            && ! $dataContainer->activeRecord instanceof Result
        ) {
            return;
        }

        if (! in_array($dataContainer->activeRecord->type, ['bs_gridSeparator', 'bs_gridStop'], true)) {
            return;
        }

        if ($dataContainer->activeRecord->bs_grid_parent > 0) {
            return;
        }

        $this->fixFormField($dataContainer->activeRecord);
    }

    /**
     * Handle the oncopy callback.
     *
     * @param int|string $formFieldId Element id of copied element.
     */
    public function onCopy(int|string $formFieldId): void
    {
        $formFieldModel = $this->repositoryManager
            ->getRepository(FormFieldModel::class)
            ->find((int) $formFieldId);

        if (! $formFieldModel instanceof FormFieldModel) {
            return;
        }

        $this->fixFormField($formFieldModel);
    }

    /**
     * Fix a relation of a form field.
     *
     * @param FormFieldModel|Result $formFieldModel The form field.
     */
    private function fixFormField(FormFieldModel|Result $formFieldModel): void
    {
        if (! in_array($formFieldModel->type, ['bs_gridSeparator', 'bs_gridStop'], true)) {
            return;
        }

        $parentModel = $this->loadGridStartFormField($formFieldModel);
        if ($parentModel === null) {
            return;
        }

        $this->repositoryManager->getConnection()->update(
            FormFieldModel::getTable(),
            [
                'bs_grid_parent' => $parentModel->id,
                'tstamp'         => time(),
            ],
            [
                'id' => $formFieldModel->id,
            ],
        );
    }

    /**
     * Load the closest grid start form field.
     *
     * @param FormFieldModel|Result $formFieldModel The form field model.
     *
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    private function loadGridStartFormField(FormFieldModel|Result $formFieldModel): FormFieldModel|null
    {
        $constraints = ['.pid=?', '.type=?', '.sorting < ?'];
        $values      = [$formFieldModel->pid, 'bs_gridStart', $formFieldModel->sorting];

        return $this->repositoryManager
            ->getRepository(FormFieldModel::class)
            ->findOneBy($constraints, $values, ['order' => '.sorting DESC']);
    }
}
