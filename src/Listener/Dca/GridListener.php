<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\Framework\Adapter;
use Contao\DataContainer;
use Contao\Input;
use Contao\Model\Collection;
use Contao\StringUtil;
use Contao\ThemeModel;
use ContaoBootstrap\Core\Environment;
use ContaoBootstrap\Core\Environment\ThemeContext;
use ContaoBootstrap\Grid\Model\GridModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;

use function array_combine;
use function array_filter;
use function array_map;
use function array_merge;
use function array_unique;
use function array_values;
use function range;
use function sprintf;

/**
 * Data container helper for grid.
 */
final class GridListener
{
    /** @param Adapter<Input> $inputAdapter */
    public function __construct(
        private readonly Environment $environment,
        private readonly RepositoryManager $repositories,
        private readonly Adapter $inputAdapter,
    ) {
    }

    /**
     * Enter a bootstrap environment context.
     */
    public function enterContext(): void
    {
        if ($this->inputAdapter->get('act') !== 'edit') {
            return;
        }

        $model = $this->repositories->getRepository(GridModel::class)->find((int) $this->inputAdapter->get('id'));
        /** @psalm-var GridModel|null $model */
        if (! $model) {
            return;
        }

        $this->environment->enterContext(ThemeContext::forTheme((int) $model->pid));
    }

    /**
     * Initialize the palette.
     */
    public function initializePalette(): void
    {
        if ($this->inputAdapter->get('act') !== 'edit') {
            return;
        }

        $model = $this->repositories->getRepository(GridModel::class)->find((int) $this->inputAdapter->get('id'));
        if (! $model instanceof GridModel) {
            return;
        }

        $sizes = array_map(
            static function (string $value): string {
                return $value . 'Size';
            },
            StringUtil::deserialize($model->sizes, true),
        );

        PaletteManipulator::create()
            ->addField($sizes, 'sizes')
            ->applyToPalette('default', 'tl_bs_grid');
    }

    /**
     * Generate the label.
     *
     * @param array<string,mixed> $row Data row.
     */
    public function generateLabel(array $row): string
    {
        return sprintf(
            '%s <div class="tl_gray">%s</div>',
            $row['title'],
            $row['description'],
        );
    }

    /**
     * Get all sizes.
     *
     * @return list<string>
     */
    public function getSizes(DataContainer $dataContainer): array
    {
        $sizes = [];
        if ($this->inputAdapter->get('act')  === 'edit') {
            $theme = $this->repositories->getRepository(ThemeModel::class)->find((int) $dataContainer->currentPid);
            if ($theme === null) {
                return [];
            }

            $sizes = array_values(array_filter(StringUtil::deserialize($theme->bs_grid_sizes, true)));
            if (! $sizes) {
                $sizes = $this->environment->getConfig()->get(['grid', 'sizes'], []);
            }

            return $sizes;
        }

        $themes = $this->repositories->getRepository(ThemeModel::class)->findAll();
        if (! $themes instanceof Collection) {
            return [];
        }

        foreach ($themes as $theme) {
            $sizes = array_merge($sizes, StringUtil::deserialize($theme->bs_grid_sizes, true));
        }

        return array_values(array_unique($sizes));
    }

    /**
     * Get all widths.
     *
     * @return array<string|int,string|int>
     */
    public function getWidths(): array
    {
        $columns = $this->getColumns();
        $values  = ['equal', 'variable', 'null'];
        $values  = array_merge($values, range(1, $columns));

        return array_combine($values, $values);
    }

    /**
     * Get the order options.
     *
     * @return list<int>
     */
    public function getOrders(): array
    {
        $columns = $this->getColumns();

        return range(1, $columns);
    }

    /**
     * Get offset values.
     *
     * @return array<string,list<string|int>>
     */
    public function getOffsets(): array
    {
        $columns = $this->getColumns();
        $values  = array_merge(
            ['null'],
            range(1, $columns),
        );

        return [
            'align' => [
                'ml-auto',
                'mr-auto',
            ],
            'offset' => $values,
        ];
    }

    /**
     * Get the number of defined columns.
     */
    private function getColumns(): int
    {
        return (int) $this->environment->getConfig()->get(['grid', 'columns'], 12);
    }
}
