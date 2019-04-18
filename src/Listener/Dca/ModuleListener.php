<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2019 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\ModuleModel;

/**
 * Data container helper class for module.
 *
 * @package ContaoBootstrap\Grid\Dca
 */
class ModuleListener extends AbstractDcaListener
{
    /**
     * Get all modules for the grid module.
     *
     * @param \MultiColumnWizard $multiColumnWizard Multicolumnwizard.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getAllModules(\MultiColumnWizard $multiColumnWizard = null): array
    {
        if ($multiColumnWizard
            && $multiColumnWizard->dataContainer
            && $multiColumnWizard->dataContainer->activeRecord) {
            $collection = ModuleModel::findBy(
                ['tl_module.pid = ?', 'tl_module.id != ?'],
                [
                    $multiColumnWizard->dataContainer->activeRecord->pid,
                    $multiColumnWizard->dataContainer->activeRecord->id
                ]
            );
        } else {
            $collection = ModuleModel::findAll();
        }

        $modules = [
            'grid' => [
                'separator' => $GLOBALS['TL_LANG']['tl_module']['bs_separatorTitle']
            ]
        ];

        if ($collection) {
            foreach ($collection as $model) {
                $label = isset($GLOBALS['TL_LANG']['FMD'][$model->type][0])
                    ? $GLOBALS['TL_LANG']['FMD'][$model->type][0]
                    : $model->type;

                $modules['module'][$model->id] = sprintf('%s [%s]', $model->name, $label);
            }
        }

        return $modules;
    }
}
