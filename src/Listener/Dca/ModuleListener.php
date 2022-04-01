<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DataContainer;
use Contao\ModuleModel;
use MenAtWork\MultiColumnWizardBundle\Contao\Widgets\MultiColumnWizard;

use function sprintf;

/**
 * Data container helper class for module.
 */
class ModuleListener extends AbstractDcaListener
{
    /**
     * Initialize the data container.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function initialize(): void
    {
        $paletteManipulator = PaletteManipulator::create()
            ->addField('bs_grid', 'template_legend', PaletteManipulator::POSITION_APPEND);

        if (isset($GLOBALS['TL_DCA']['tl_module']['palettes']['newslist'])) {
            $paletteManipulator->applyToPalette('newslist', 'tl_module');
        }

        if (! isset($GLOBALS['TL_DCA']['tl_module']['palettes']['newsarchive'])) {
            return;
        }

        $paletteManipulator->applyToPalette('newsarchive', 'tl_module');
    }

    /**
     * Set grid options.
     *
     * @param mixed         $value         Given value.
     * @param DataContainer $dataContainer The data container.
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function setGridWidgetOptions($value, DataContainer $dataContainer)
    {
        if ($dataContainer->activeRecord->type === 'bs_grid') {
            $GLOBALS['TL_DCA']['tl_module']['fields'][$dataContainer->field]['eval']['mandatory'] = true;
        }

        return $value;
    }

    /**
     * Get all modules for the grid module.
     *
     * @param MultiColumnWizard $multiColumnWizard Multicolumnwizard.
     *
     * @return array<string,array<int|string,string>>
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getAllModules(?MultiColumnWizard $multiColumnWizard = null): array
    {
        if (
            $multiColumnWizard
            && $multiColumnWizard->dataContainer
            && $multiColumnWizard->dataContainer->activeRecord
        ) {
            $collection = ModuleModel::findBy(
                ['tl_module.pid = ?', 'tl_module.id != ?'],
                [
                    $multiColumnWizard->dataContainer->activeRecord->pid,
                    $multiColumnWizard->dataContainer->activeRecord->id,
                ]
            );
        } else {
            $collection = ModuleModel::findAll();
        }

        $modules = [
            'grid' => [
                'separator' => $GLOBALS['TL_LANG']['tl_module']['bs_separatorTitle'],
            ],
        ];

        if ($collection) {
            foreach ($collection as $model) {
                $label = $GLOBALS['TL_LANG']['FMD'][$model->type][0] ?? $model->type;

                $modules['module'][$model->id] = sprintf('%s [%s]', $model->name, $label);
            }
        }

        return $modules;
    }
}
