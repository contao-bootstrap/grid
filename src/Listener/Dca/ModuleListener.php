<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DataContainer;
use Contao\Model\Collection;
use Contao\ModuleModel;

use function sprintf;

/**
 * Data container helper class for module.
 */
final class ModuleListener extends AbstractDcaListener
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
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function setGridWidgetOptions(mixed $value, DataContainer $dataContainer): mixed
    {
        if ($dataContainer->activeRecord && $dataContainer->activeRecord->type === 'bs_grid') {
            $GLOBALS['TL_DCA']['tl_module']['fields'][$dataContainer->field]['eval']['mandatory'] = true;
        }

        return $value;
    }

    /**
     * Get all modules for the grid module.
     *
     * @return array<string,array<int|string,string>>
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getAllModules(DataContainer|null $dataContainer = null): array
    {
        if ($dataContainer && $dataContainer->activeRecord) {
            $collection = $this->repositories->getRepository(ModuleModel::class)->findBy(
                ['.pid = ?', '.id != ?'],
                [
                    $dataContainer->activeRecord->pid,
                    $dataContainer->activeRecord->id,
                ],
            );
        } else {
            $collection = $this->repositories->getRepository(ModuleModel::class)->findAll();
        }

        $modules = [
            'grid' => [
                'separator' => $GLOBALS['TL_LANG']['tl_module']['bs_separatorTitle'],
            ],
        ];

        if ($collection instanceof Collection) {
            foreach ($collection as $model) {
                $label = $GLOBALS['TL_LANG']['FMD'][$model->type][0] ?? $model->type;

                $modules['module'][$model->id] = sprintf('%s [%s]', $model->name, $label);
            }
        }

        return $modules;
    }
}
