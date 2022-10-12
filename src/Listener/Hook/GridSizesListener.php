<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Hook;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Model;
use Contao\StringUtil;
use Contao\ThemeModel;
use ContaoBootstrap\Core\Environment;
use ContaoBootstrap\Grid\Model\GridModel;
use Doctrine\DBAL\Connection;

use function array_merge;
use function array_unique;
use function array_values;
use function sprintf;

/**
 * Class GridSizesListener initializes all dynamic grid size columns
 *
 * It creates the size fields in the data container definition and creates database fields if not exist. Dynamically
 * creates size field doesn't get deleted automatically to prevent data loss.
 */
final class GridSizesListener
{
    /**
     * @param Connection  $connection  Database connection.
     * @param Environment $environment Contao bootstrap environment.
     */
    public function __construct(private readonly Connection $connection, private readonly Environment $environment)
    {
    }

    /**
     * Initialize all grid sizes.
     *
     * @param string $dataContainer The data container name.
     *
     * @Hook("loadDataContainer", priority=100)
     */
    public function initializeSizes(string $dataContainer): void
    {
        if ($dataContainer !== GridModel::getTable()) {
            return;
        }

        $schemaManager = $this->connection->createSchemaManager();
        if (! $schemaManager->tablesExist([GridModel::getTable(), 'tl_theme'])) {
            return;
        }

        foreach ($this->getSizes() as $size) {
            $this->createDcaField($size);
            $this->createDatabaseField($size);
        }
    }

    /**
     * Create the dca field definition.
     *
     * @param string $size The grid size.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function createDcaField(string $size): void
    {
        $sizeLabel = $size . 'Size';

        $GLOBALS['TL_DCA']['tl_bs_grid']['fields'][$sizeLabel] = [
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'sql'       => 'blob NULL',
            'eval'      => [
                'includeBlankOption' => true,
                'dragAndDrop'        => true,
                'columnFields'       => [
                    'width'  => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_bs_grid']['width'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.listeners.dca.grid',
                            'getWidths',
                        ],
                        'eval'             => [
                            'style'              => 'width: 100%;',
                            'isAssociative'      => true,
                            'chosen'             => true,
                            'includeBlankOption' => true,
                        ],
                    ],
                    'offset' => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_bs_grid']['offset'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.listeners.dca.grid',
                            'getOffsets',
                        ],
                        'reference'        => ['null' => '0 '],
                        'eval'             => [
                            'style'              => 'width: 100%;',
                            'includeBlankOption' => true,
                            'chosen'             => true,
                            'isAssociative'      => false,
                        ],
                    ],
                    'order'  => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_bs_grid']['order'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'contao_bootstrap.grid.listeners.dca.grid',
                            'getOrders',
                        ],
                        'eval'             => [
                            'style'              => 'width: 100%',
                            'includeBlankOption' => true,
                            'chosen'             => true,
                        ],
                    ],
                    'align'  => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_bs_grid']['align'],
                        'inputType' => 'select',
                        'options'   => ['start', 'center', 'end'],
                        'eval'      => [
                            'style'              => 'width: 100%',
                            'includeBlankOption' => true,
                            'chosen'             => true,
                        ],
                    ],
                    'class'  => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_bs_grid']['class'],
                        'exclude'   => true,
                        'default'   => '',
                        'inputType' => 'text',
                        'eval'      => ['style' => 'width: 100%'],
                    ],
                    'reset'  => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_bs_grid']['reset'],
                        'exclude'   => true,
                        'default'   => '',
                        'inputType' => 'select',
                        'options'   => ['1', '2'],
                        'reference' => &$GLOBALS['TL_LANG']['tl_bs_grid']['resets'],
                        'eval'      => [
                            'includeBlankOption' => true,
                            'style'              => 'width: 100%',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Create the database field for the grid size if not exists.
     *
     * @param string $size The grid size.
     */
    public function createDatabaseField(string $size): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        if (! $schemaManager->tablesExist(GridModel::getTable())) {
            return;
        }

        $columns = $schemaManager->listTableColumns(GridModel::getTable());
        if (isset($columns[$size . 'size'])) {
            return;
        }

        $this->connection->executeStatement(
            sprintf('ALTER TABLE %s ADD %sSize BLOB DEFAULT NULL', GridModel::getTable(), $size),
        );
    }

    /**
     * Get all sizes.
     *
     * @return list<string>
     */
    public function getSizes(): array
    {
        $sizes  = $this->environment->getConfig()->get(['grid', 'sizes'], []);
        $themes = ThemeModel::findAll() ?: [];

        if ($themes instanceof Model) {
            $themes = [$themes];
        }

        foreach ($themes as $theme) {
            $sizes = array_merge($sizes, StringUtil::deserialize($theme->bs_grid_sizes, true));
        }

        return array_values(array_unique($sizes));
    }
}
