<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Exception;
use Override;

final class GridWrapperMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $connection, private readonly bool $enableMigration = false)
    {
    }

    /** @throws Exception */
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

        $count = $this->connection->fetchOne(
            "SELECT COUNT(*) FROM tl_content WHERE type = 'bs_gridStart'",
        );

        return $count > 0;
    }

    /** @throws Exception */
    #[Override]
    public function run(): MigrationResult
    {
        $this->connection->transactional(function (): void {
            while ($startElement = $this->fetchNextGridStart()) {
                $this->migrateGridStart($startElement);
            }
        });

        return $this->createResult(true);
    }

    /**
     * @return array<string, mixed>|false
     *
     * @throws Exception
     */
    private function fetchNextGridStart(): array|false
    {
        return $this->connection->fetchAssociative(
            "SELECT * FROM tl_content WHERE type = 'bs_gridStart' ORDER BY id LIMIT 1",
        );
    }

    /**
     * @param array<string, mixed> $start
     *
     * @throws Exception
     */
    private function migrateGridStart(array $start): void
    {
        $startId      = (int) $start['id'];
        $pid          = (int) $start['pid'];
        $ptable       = (string) $start['ptable'];
        $startSorting = (int) $start['sorting'];
        $tstamp       = (int) $start['tstamp'];

        $stop = $this->connection->fetchAssociative(
            "SELECT * FROM tl_content WHERE bs_grid_parent = :parent AND type = 'bs_gridStop'",
            ['parent' => $startId],
        );

        $separators = $this->connection->fetchAllAssociative(
            "SELECT * FROM tl_content WHERE bs_grid_parent = :parent AND type = 'bs_gridSeparator' ORDER BY sorting ASC",
            ['parent' => $startId],
        );

        $this->connection->executeStatement(
            "UPDATE tl_content SET type = 'bs_grid_wrapper' WHERE id = :id",
            ['id' => $startId],
        );

        $stopSorting    = $stop !== false ? (int) $stop['sorting'] : PHP_INT_MAX;
        $bounds         = [$startSorting];
        $wrapperSorting = 128;

        foreach ($separators as $sep) {
            $bounds[] = (int) $sep['sorting'];
        }

        $bounds[] = $stopSorting;

        // Slot 0: elements before first separator (or before stop if no separators)
        $slot0 = $this->fetchSlotElements($pid, $ptable, $bounds[0], $bounds[1]);
        $wrapperSorting = $this->placeSlotElements($slot0, $startId, null, $wrapperSorting, $tstamp);

        // One slot per separator: elements after the separator until next separator or stop
        foreach ($separators as $i => $sep) {
            $sepId        = (int) $sep['id'];
            $nextBound    = $bounds[$i + 2];
            $slotElements = $this->fetchSlotElements($pid, $ptable, (int) $sep['sorting'], $nextBound);
            $wrapperSorting = $this->placeSlotElements($slotElements, $startId, $sepId, $wrapperSorting, $tstamp);
        }

        if ($stop !== false) {
            $this->deleteElement((int) $stop['id']);
        }
    }

    /**
     * Places slot elements into the wrapper according to count:
     * - 0 elements: separator (if any) is deleted
     * - 1 element:  element moves directly into wrapper, separator (if any) is deleted
     * - >1 elements: separator becomes element_group (or new group is created), elements move into it
     *
     * Returns the next available wrapper sorting value.
     *
     * @param array<int, array<string, mixed>> $elements
     *
     * @throws Exception
     */
    private function placeSlotElements(array $elements, int $wrapperId, int|null $separatorId, int $wrapperSorting, int $tstamp): int
    {
        if (count($elements) > 1) {
            if ($separatorId !== null) {
                $this->connection->executeStatement(
                    "UPDATE tl_content SET type = 'element_group', pid = :pid, ptable = 'tl_content', sorting = :sorting WHERE id = :id",
                    ['pid' => $wrapperId, 'sorting' => $wrapperSorting, 'id' => $separatorId],
                );
                $groupId = $separatorId;
            } else {
                $groupId = $this->insertElementGroup($wrapperId, 'tl_content', $wrapperSorting, $tstamp);
            }

            $wrapperSorting += 128;
            $this->moveElementsToParent($elements, $groupId, 'tl_content');
        } elseif (count($elements) === 1) {
            if ($separatorId !== null) {
                $this->deleteElement($separatorId);
            }

            $this->moveElementsToParent($elements, $wrapperId, 'tl_content', $wrapperSorting);
            $wrapperSorting += 128;
        } else {
            if ($separatorId !== null) {
                $this->deleteElement($separatorId);
            }
        }

        return $wrapperSorting;
    }

    /**
     * @return array<int, array<string, mixed>>
     *
     * @throws Exception
     */
    private function fetchSlotElements(int $pid, string $ptable, int $fromSorting, int $toSorting): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT * FROM tl_content WHERE pid = :pid AND ptable = :ptable AND sorting > :from AND sorting < :to ORDER BY sorting ASC',
            ['pid' => $pid, 'ptable' => $ptable, 'from' => $fromSorting, 'to' => $toSorting],
        );
    }

    /** @throws Exception */
    private function insertElementGroup(int $pid, string $ptable, int $sorting, int $tstamp): int
    {
        $this->connection->executeStatement(
            "INSERT INTO tl_content (pid, ptable, sorting, tstamp, type) VALUES (:pid, :ptable, :sorting, :tstamp, 'element_group')",
            ['pid' => $pid, 'ptable' => $ptable, 'sorting' => $sorting, 'tstamp' => $tstamp],
        );

        return (int) $this->connection->lastInsertId();
    }

    /**
     * @param array<int, array<string, mixed>> $elements
     *
     * @throws Exception
     */
    private function moveElementsToParent(array $elements, int $newPid, string $newPtable, int $startSorting = 128): void
    {
        $sorting = $startSorting;

        foreach ($elements as $element) {
            $this->connection->executeStatement(
                'UPDATE tl_content SET pid = :pid, ptable = :ptable, sorting = :sorting WHERE id = :id',
                ['pid' => $newPid, 'ptable' => $newPtable, 'sorting' => $sorting, 'id' => $element['id']],
            );
            $sorting += 128;
        }
    }

    /** @throws Exception */
    private function deleteElement(int $id): void
    {
        $this->connection->executeStatement(
            'DELETE FROM tl_content WHERE id = :id',
            ['id' => $id],
        );
    }
}
