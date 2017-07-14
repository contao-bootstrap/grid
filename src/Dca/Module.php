<?php

/**
 * @package    Website
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoBootstrap\Grid\Dca;

use Contao\ModuleModel;

/**
 * Data container helper class for module.
 *
 * @package ContaoBootstrap\Grid\Dca
 */
class Module extends AbstractDcaHelper
{
    /**
     * Get all modules for the grid module.
     *
     * @param \MultiColumnWizard $multiColumnWizard Multicolumnwizard.
     *
     * @return array
     */
    public function getAllModules(\MultiColumnWizard $multiColumnWizard = null)
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
                $modules['module'][$model->id] = sprintf(
                    '%s [%s]',
                    $model->name,
                    isset($GLOBALS['TL_LANG']['FMD'][$model->type][0])
                        ? $GLOBALS['TL_LANG']['FMD'][$model->type][0]
                        : $model->type
                );
            }
        }

        return $modules;
    }
}
