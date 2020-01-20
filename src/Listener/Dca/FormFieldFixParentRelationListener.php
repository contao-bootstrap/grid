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
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * FormFieldFixParentRelationListener constructor.
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

        $this->fixFormField($dataContainer->activeRecord);
    }

    /**
     * Handle the oncopy callback.
     *
     * @param int|string $formFieldId Element id of copied element.
     *
     * @return void
     */
    public function onCopy($formFieldId): void
    {
        $formFieldModel = $this->repositoryManager->getRepository(FormFieldModel::class)->find($formFieldId);
        if ($formFieldModel === null) {
            return;
        }

        $this->fixFormField($formFieldModel);
    }

    /**
     * Fix a relation of a form field.
     *
     * @param FormFieldModel|Result $formFieldModel The form field.
     *
     * @return void
     */
    private function fixFormField($formFieldModel): void
    {
        if (!in_array($formFieldModel->type, ['bs_gridSeparator', 'bs_gridStop'], true)) {
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
                'tstamp'         => time()
            ],
            [
                'id' => $formFieldModel->id
            ]
        );
    }

    /**
     * Load the closest grid start form field.
     *
     * @param FormFieldModel|Result $formFieldModel The form field model.
     *
     * @return FormFieldModel|null
     */
    private function loadGridStartFormField($formFieldModel) : ?FormFieldModel
    {
        $constraints = ['.pid=?', '.type=?', '.sorting < ?'];
        $values      = [$formFieldModel->pid, 'bs_gridStart', $formFieldModel->sorting];

        return $this->repositoryManager
            ->getRepository(FormFieldModel::class)
            ->findOneBy($constraints, $values, ['order' => '.sorting DESC']);
    }
}
