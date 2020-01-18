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
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Constructor.
     *
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
     *
     * @return void
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
        $constraints = ['.pid=?', 'FIND_IN_SET(type, \'bs_gridStart,bs_gridSeparator,bs_gridStop\')'];
        $values      = [$parentId, $parentTable];

        return $this->repositoryManager
            ->getRepository(FormFieldModel::class)
            ->findBy($constraints, $values, ['order' => '.sorting']);
    }
}
