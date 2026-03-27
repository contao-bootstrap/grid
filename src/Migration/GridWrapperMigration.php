<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

use Override;

final class GridWrapperMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $connection, private readonly bool $enableMigration = false)
    {
    }

    #[Override]
    public function shouldRun(): bool
    {
        if ($this->enableMigration === false) {
            return false;
        }

        $schemaManager = $this->connection->createSchemaManager();
        if (! $schemaManager->tablesExist(['tl_bs_grid', 'tl_content'])) {
            return false;
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder
                ->select('COUNT(tc.id) as count')
                ->from('tl_content', 'tc')
                ->where($queryBuilder->expr()->eq('tc.type', ':type'))
                ->setParameter('type', 'bs_gridStart')
                ->executeQuery()
                ->fetchOne() > 0;
    }

    #[Override]
    public function run(): MigrationResult
    {
        $sql = <<<SQL
    SELECT
        grid_start.id        AS grid_start_id,
        grid_start.pid       AS pid,
        grid_start.ptable    AS ptable,
        el.id                AS element_id,
        el.type              AS element_type,
        el.sorting           AS element_sorting,
        (
            SELECT grid_stop.id
            FROM tl_content grid_stop
            WHERE grid_stop.pid     = grid_start.pid
              AND grid_stop.ptable  = grid_start.ptable
              AND grid_stop.type    = 'bs_gridStop'
              AND grid_stop.sorting > grid_start.sorting
            ORDER BY grid_stop.sorting ASC
            LIMIT 1
        )                    AS grid_stop_id
    FROM tl_content grid_start
    LEFT JOIN tl_content el
        ON  el.pid     = grid_start.pid
        AND el.ptable  = grid_start.ptable
        AND el.sorting > grid_start.sorting
        AND el.sorting < (
            SELECT MIN(grid_stop.sorting)
            FROM tl_content grid_stop
            WHERE grid_stop.pid     = grid_start.pid
              AND grid_stop.ptable  = grid_start.ptable
              AND grid_stop.type    = 'bs_gridStop'
              AND grid_stop.sorting > grid_start.sorting
        )
    WHERE grid_start.type = 'bs_gridStart'
    ORDER BY grid_start.pid, grid_start.ptable, grid_start.sorting, el.sorting
SQL;

        $contentElements = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $gridContainers = array_reduce($contentElements, function (array $carry, array $row) {
            $startId = $row['grid_start_id'];

            $carry[$startId] ??= [
                'start_id' => $startId,
                'stop_id'  => $row['grid_stop_id'],
                'elements' => [],
            ];

            if ($row['element_id'] !== null) {
                $carry[$startId]['elements'][] = $row;
            }

            return $carry;
        }, []);

        $elementCount = array_sum(
            array_map(
                static fn (array $gridContainer) => count($gridContainer['elements']),
                $gridContainers
            )
        );

        foreach ($gridContainers as $gridContainer) {
            $this->connection->update(
                'tl_content',
                ['type' => 'bs_grid_wrapper'],
                ['id' => $gridContainer['start_id']]
            );

            foreach ($gridContainer['elements'] as $element) {
                $this->connection->update(
                    'tl_content',
                    ['pid' => $gridContainer['start_id'], 'ptable' => 'tl_content'],
                    ['id' => $element['element_id']]
                );
            }

            $this->connection->delete('tl_content', ['id' => $gridContainer['stop_id']]);
        }

        return $this->createResult(
            true,
            'Migrated ' . count($gridContainers) . ' grid containers and ' . $elementCount . ' elements.'
        );
    }
}
