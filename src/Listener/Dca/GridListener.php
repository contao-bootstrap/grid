<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\Input;
use Contao\StringUtil;
use Contao\ThemeModel;
use ContaoBootstrap\Core\Environment;
use ContaoBootstrap\Core\Environment\ThemeContext;
use ContaoBootstrap\Grid\Model\GridModel;

use function array_combine;
use function array_map;
use function array_merge;
use function array_unique;
use function array_values;
use function defined;
use function range;
use function sprintf;

/**
 * Data container helper for grid.
 */
class GridListener
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
     * Enter a bootstrap environment context.
     */
    public function enterContext(): void
    {
        if (Input::get('act') !== 'edit') {
            return;
        }

        $model = GridModel::findByPk(Input::get('id'));

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
        if (Input::get('act') !== 'edit') {
            return;
        }

        $model = GridModel::findByPk(Input::get('id'));
        $sizes = array_map(
            static function ($value) {
                return $value . 'Size';
            },
            StringUtil::deserialize($model->sizes, true)
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
            $row['description']
        );
    }

    /**
     * Get all sizes.
     *
     * @return list<string>
     */
    public function getSizes(): array
    {
        $sizes = [];
        if (defined('CURRENT_ID') && Input::get('act') === 'edit') {
            $theme = ThemeModel::findByPk(CURRENT_ID);
            $sizes = StringUtil::deserialize($theme->bs_grid_sizes, true);

            if (! $sizes) {
                $sizes = $this->environment->getConfig()->get('grid.sizes', []);
            }

            return $sizes;
        }

        $themes = ThemeModel::findAll() ?: [];
        foreach ($themes as $theme) {
            $sizes = array_merge($sizes, StringUtil::deserialize($theme->bs_grid_sizes, true));
        }

        return array_values(array_unique($sizes));
    }

    /**
     * Get all widths.
     *
     * @return array<string,string>
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
     * @return array<string,list<string>>
     */
    public function getOffsets(): array
    {
        $columns = $this->getColumns();
        $values  = array_merge(
            ['null'],
            range(1, $columns)
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
        return (int) $this->environment->getConfig()->get('grid.columns', 12);
    }
}
