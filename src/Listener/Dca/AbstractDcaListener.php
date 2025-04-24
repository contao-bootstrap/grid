<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\DataContainer;
use Contao\Model\Collection;
use ContaoBootstrap\Core\Environment;
use ContaoBootstrap\Grid\Model\GridModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;

use function range;
use function sprintf;

/**
 * GridOptionsProvider provides grid related options callbacks.
 */
abstract class AbstractDcaListener
{
    public function __construct(
        private readonly Environment $environment,
        protected readonly RepositoryManager $repositories,
    ) {
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
            (int) $this->environment->getConfig()->get(['grid', 'columns'], 12),
        );
    }

    /**
     * Get grid breakpoints.
     *
     * @return list<string>
     */
    public function getGridSizes(): array
    {
        return $this->environment->getConfig()->get(['grid', 'sizes'], []);
    }

    /**
     * Get all available grids.
     *
     * @return array<string,array<int|string,string>>
     */
    public function getGridOptions(): array
    {
        $collection = $this->repositories->getRepository(GridModel::class)->findAll(['order' => '.title']);
        $options    = [];

        if ($collection instanceof Collection) {
            foreach ($collection as $model) {
                $parent = sprintf(
                    '%s [ID %s]',
                    $model->getRelated('pid')->name,
                    $model->pid,
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
    public function generateGridName(string $value, DataContainer $dataContainer): string
    {
        if (! $value) {
            $value = 'grid_' . $dataContainer->id;
        }

        return $value;
    }
}
