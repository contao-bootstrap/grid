<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\DataContainer;
use ContaoBootstrap\Core\Environment;
use ContaoBootstrap\Grid\Model\GridModel;

use function range;
use function sprintf;

/**
 * GridOptionsProvider provides grid related options callbacks.
 */
abstract class AbstractDcaListener
{
    /**
     * Bootstrap environment.
     */
    private Environment $environment;

    /**
     * @param Environment $environment Bootstrap environment.
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Get range of grid columns.
     *
     * @return list<int>
     */
    public function getGridColumns(): array
    {
        return range(
            1,
            (int) $this->environment->getConfig()->get('grid.columns', 12)
        );
    }

    /**
     * Get grid breakpoints.
     *
     * @return list<string>
     */
    public function getGridSizes(): array
    {
        return $this->environment->getConfig()->get('grid.sizes', []);
    }

    /**
     * Get all available grids.
     *
     * @return array<string,array<int|string,string>>
     */
    public function getGridOptions(): array
    {
        $collection = GridModel::findAll(['order' => 'tl_bs_grid.title']);
        $options    = [];

        if ($collection) {
            foreach ($collection as $model) {
                $parent = sprintf(
                    '%s [ID %s]',
                    $model->getRelated('pid')->name,
                    $model->pid
                );

                $options[$parent][$model->id] = sprintf('%s [ID %s]', $model->title, $model->id);
            }
        }

        return $options;
    }

    /**
     * Generate a grid name if not given.
     *
     * @param string        $value         Grid name.
     * @param DataContainer $dataContainer Data container driver.
     */
    public function generateGridName($value, $dataContainer): string
    {
        if (! $value) {
            $value = 'grid_' . $dataContainer->activeRecord->id;
        }

        return $value;
    }
}
