<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Migration;

use Contao\StringUtil;
use Doctrine\DBAL\Connection;

use function array_map;
use function serialize;
use function time;

/**
 * Migrate the auto grid widths to equal.
 */
final class MigrateAutoGridWidths
{
    private const SIZES = ['xs', 'sm', 'md', 'lg', 'xl'];

    /**
     * Database connection.
     */
    private Connection $connection;

    /**
     * @param Connection $connection Database connection.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Invoke the migration script.
     */
    public function __invoke(): void
    {
        $statement = $this->connection->executeQuery('SELECT * FROM tl_bs_grid');

        while ($row = $statement->fetchAssociative()) {
            $this->migrateRow($row);
        }
    }

    /**
     * Migrate a grid definition row.
     *
     * @param array<string,mixed> $row The grid definition row.
     */
    private function migrateRow(array $row): void
    {
        $data = ['tstamp' => time()];

        foreach (self::SIZES as $size) {
            $size       .= 'Size';
            $data[$size] = $this->migrateSize($row[$size]);
        }

        $this->connection->update('tl_bs_grid', $data, ['id' => $row['id']]);
    }

    /**
     * Migrate a grid size.
     *
     * @param string|null $size The grid size definition.
     */
    private function migrateSize(?string $size): ?string
    {
        if ($size === null) {
            return null;
        }

        $columns = array_map(
            static function (array $column) {
                if ($column['width'] === 'auto') {
                    $column['width'] = 'equal';
                }

                return $column;
            },
            StringUtil::deserialize($size, true)
        );

        return serialize($columns);
    }
}
