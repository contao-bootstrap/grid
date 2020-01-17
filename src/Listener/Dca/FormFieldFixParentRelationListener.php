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
     * Handle the oncopy callback.
     *
     * @param int|string $elementId Element id of copied element.
     *
     * @return void
     */
    public function onCopy($elementId): void
    {
        /** @var FormFieldModel|null $formFieldModel */
        $elementId    = (int) $elementId;
        $formFieldModel = $this->repositoryManager->getRepository(FormFieldModel::class)->find($elementId);
        if ($formFieldModel === null || !in_array($formFieldModel->type, ['bs_gridSeparator', 'bs_gridStop'], true)) {
            return;
        }

        $parentModel = $this->loadParentModel($formFieldModel);
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

    private function loadParentModel(FormFieldModel $formFieldModel) : ?FormFieldModel
    {
        $constraints = ['.pid=?', '.type=?', '.sorting < ?'];
        $values      = [$formFieldModel->pid, 'bs_gridStart', $formFieldModel->sorting];

        return $this->repositoryManager
            ->getRepository(FormFieldModel::class)
            ->findOneBy($constraints, $values, ['order' => '.sorting DESC']);
    }
}
