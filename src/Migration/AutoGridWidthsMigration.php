<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Override;

use function array_map;
use function serialize;
use function time;

/**
 * Migrate the auto grid widths to equal.
 */
final class AutoGridWidthsMigration extends AbstractMigration
{
    private const SIZES = ['xs', 'sm', 'md', 'lg', 'xl'];

    /**
     * Database connection.
     */
    private Connection $connection;

    /** @param Connection $connection Database connection. */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    #[Override]
    public function shouldRun(): bool
    {
        if (! $this->connection->createSchemaManager()->tablesExist(['tl_bs_grid'])) {
            return false;
        }

        $statement = $this->connection->executeQuery('SELECT * FROM tl_bs_grid');

        while ($row = $statement->fetchAssociative()) {
            foreach (self::SIZES as $size) {
                $size .= 'Size';
                if (! isset($row[$size])) {
                    continue;
                }

                foreach (StringUtil::deserialize($row[$size], true) as $column) {
                    if ($column['width'] === 'auto') {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    #[Override]
    public function run(): MigrationResult
    {
        $statement = $this->connection->executeQuery('SELECT * FROM tl_bs_grid');

        while ($row = $statement->fetchAssociative()) {
            $this->migrateRow($row);
        }

        return $this->createResult(true);
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
    private function migrateSize(string|null $size): string|null
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
            StringUtil::deserialize($size, true),
        );

        return serialize($columns);
    }
}
