<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Exception;
use Override;

use function count;

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

        if (! $schemaManager->tablesExist(['tl_content'])) {
            return false;
        }

        $count = $this->connection->fetchOne(
            "SELECT COUNT(*) FROM tl_content WHERE type IN ('bs_gridStart', 'bs_gridStop', 'bs_gridSeparator')",
        );

        return $count > 0;
    }

    /** @throws Exception */
    public function run(): MigrationResult
    {
        $this->connection->transactional(function (): void {
            $roots = $this->connection->fetchAllAssociative(
                "SELECT DISTINCT pid, ptable FROM tl_content 
                            WHERE type IN ('bs_gridStart', 'bs_gridStop', 'bs_gridSeparator')",
            );

            foreach ($roots as $root) {
                $elements = $this->fetchElements((int) $root['pid'], $root['ptable']);
                $this->processElements($elements, (int) $root['pid'], $root['ptable']);
            }
        });

        return $this->createResult(true);
    }

    /**
     * @return array<int, array<string, mixed>>
     *
     * @throws Exception
     */
    private function fetchElements(int $pid, string $ptable): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT * FROM tl_content WHERE pid = :pid AND ptable = :ptable ORDER BY sorting ASC',
            ['pid' => $pid, 'ptable' => $ptable],
        );
    }

    /**
     * Main recursive processor. Works with an index pointer through the flat list.
     *
     * @param array<int, array<string, mixed>> $elements
     *
     * @throws Exception
     */
    private function processElements(array $elements, int $pid, string $ptable): void
    {
        $idx   = 0;
        $count = count($elements);

        while ($idx < $count) {
            $element = $elements[$idx];

            match ($element['type']) {
                'bs_gridStart'     => $idx = $this->handleGridStart($elements, $idx, $pid, $ptable),
                'bs_gridSeparator' => $idx = $this->handleSeparator($elements, $idx, $pid, $ptable),
                'bs_gridStop'      => $idx = $this->handleGridStop($elements, $idx),
                default            => $idx++,
            };
        }
    }

    /**
     * Handles a bs_gridStart: converts it to bs_grid_wrapper,
     * collects all children until matching bs_gridStop and moves them into the wrapper.
     * If there are bs_gridSeparators at the top level, elements before the first separator
     * are wrapped in an implicit element_group.
     *
     * @param array<int, array<string, mixed>> $elements
     *
     * @throws Exception
     */
    private function handleGridStart(array $elements, int $index): int
    {
        $startElement = $elements[$index];
        $wrapperId    = (int) $startElement['id'];

        $this->connection->executeStatement(
            "UPDATE tl_content SET type = 'bs_grid_wrapper' WHERE id = :id",
            ['id' => $wrapperId],
        );

        // Collect all children until matching bs_gridStop
        [$children, $nextIndex] = $this->collectUntilMatchingStop($elements, $index + 1);

        // Move children into wrapper with fresh sorting
        $this->moveElementsToParent($children, $wrapperId, 'tl_content');

        // Only wrap leading elements in an implicit group if there are top-level separators
        $this->wrapLeadingElementsInGroup($wrapperId, $startElement);

        // Now recursively process the children inside the wrapper
        $movedElements = $this->fetchElements($wrapperId, 'tl_content');
        $this->processElements($movedElements, $wrapperId, 'tl_content');

        return $nextIndex;
    }

    /**
     * Wraps all elements before the first bs_gridSeparator inside a wrapper
     * into a newly created element_group.
     * If there is no top-level bs_gridSeparator, nothing is done.
     *
     * @param array<string, mixed> $referenceElement Used to copy tstamp for the new row
     *
     * @throws Exception
     */
    private function wrapLeadingElementsInGroup(int $wrapperPid, array $referenceElement): void
    {
        $children = $this->fetchElements($wrapperPid, 'tl_content');

        // If there is no bs_gridSeparator at depth 0, elements stay directly in the wrapper
        if (! $this->hasTopLevelSeparator($children)) {
            return;
        }

        // Collect elements before the first bs_gridSeparator at depth 0
        $leadingElements = [];
        $depth           = 0;

        foreach ($children as $child) {
            $type = $child['type'];

            if ($type === 'bs_gridStart') {
                $depth++;
                $leadingElements[] = $child;
            } elseif ($type === 'bs_gridStop') {
                if ($depth === 0) {
                    break;
                }

                $depth--;
                $leadingElements[] = $child;
            } elseif ($type === 'bs_gridSeparator') {
                if ($depth === 0) {
                    // Stop here, do not include the separator itself
                    break;
                }

                $leadingElements[] = $child;
            } else {
                $leadingElements[] = $child;
            }
        }

        // Nothing to wrap (grid starts directly with a separator)
        if (empty($leadingElements)) {
            return;
        }

        // Create a new element_group as first child of the wrapper
        // sorting = 64 so it sits before the existing children which start at 128
        $this->connection->executeStatement(
            "INSERT INTO tl_content (pid, ptable, sorting, tstamp, type) 
VALUES (:pid, :ptable, :sorting, :tstamp, 'element_group')",
            [
                'pid'     => $wrapperPid,
                'ptable'  => 'tl_content',
                'sorting' => 64,
                'tstamp'  => $referenceElement['tstamp'],
            ],
        );

        $groupId = (int) $this->connection->lastInsertId();

        $this->moveElementsToParent($leadingElements, $groupId, 'tl_content');
    }

    /**
     * Checks whether there is at least one bs_gridSeparator at depth 0 in the given element list.
     *
     * @param array<int, array<string, mixed>> $elements
     */
    private function hasTopLevelSeparator(array $elements): bool
    {
        $depth = 0;

        foreach ($elements as $element) {
            $type = $element['type'];

            if ($type === 'bs_gridStart') {
                $depth++;
            } elseif ($type === 'bs_gridStop') {
                $depth--;
            } elseif ($type === 'bs_gridSeparator' && $depth === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handles a bs_gridSeparator: converts it to element_group,
     * collects all following siblings until next bs_gridSeparator or bs_gridStop
     * and moves them into the group.
     *
     * @param array<int, array<string, mixed>> $elements
     *
     * @throws Exception
     */
    private function handleSeparator(array $elements, int $index): int
    {
        $separatorElement = $elements[$index];
        $groupId          = (int) $separatorElement['id'];

        $this->connection->executeStatement(
            "UPDATE tl_content SET type = 'element_group' WHERE id = :id",
            ['id' => $groupId],
        );

        // Collect following siblings until next separator or stop (depth-aware)
        [$children, $nextIndex] = $this->collectUntilNextSeparatorOrStop($elements, $index + 1);

        // Move collected elements into the element_group
        $this->moveElementsToParent($children, $groupId, 'tl_content');

        // Recursively process elements inside the group (e.g. nested grids inside a group)
        $movedElements = $this->fetchElements($groupId, 'tl_content');
        $this->processElements($movedElements, $groupId, 'tl_content');

        return $nextIndex;
    }

    /**
     * Handles a bs_gridStop: deletes it and advances the index.
     *
     * @param array<int, array<string, mixed>> $elements
     *
     * @throws Exception
     */
    private function handleGridStop(array $elements, int $index): int
    {
        $this->deleteElement((int) $elements[$index]['id']);

        return $index + 1;
    }

    /**
     * Collects all elements from $startIndex until the matching bs_gridStop (depth-aware).
     * The matching bs_gridStop itself is deleted and NOT included in the result.
     *
     * @param array<int, array<string, mixed>> $elements
     *
     * @return array{0: array<int, array<string, mixed>>, 1: int}
     *
     * @throws Exception
     */
    private function collectUntilMatchingStop(array $elements, int $startIndex): array
    {
        $children = [];
        $depth    = 1;
        $idx      = $startIndex;
        $count    = count($elements);

        while ($idx < $count && $depth > 0) {
            $type = $elements[$idx]['type'];

            if ($type === 'bs_gridStart') {
                $depth++;
                $children[] = $elements[$idx];
            } elseif ($type === 'bs_gridStop') {
                $depth--;
                if ($depth === 0) {
                    $this->deleteElement((int) $elements[$idx]['id']);
                    $idx++;
                    break;
                }

                $children[] = $elements[$idx];
            } else {
                $children[] = $elements[$idx];
            }

            $idx++;
        }

        return [$children, $idx];
    }

    /**
     * Collects elements from $startIndex until the next bs_gridSeparator or bs_gridStop at depth 0.
     * Nested grids are included as a block (depth-aware).
     *
     * @param array<int, array<string, mixed>> $elements
     *
     * @return array{0: array<int, array<string, mixed>>, 1: int}
     */
    private function collectUntilNextSeparatorOrStop(array $elements, int $startIndex): array
    {
        $children = [];
        $depth    = 0;
        $idx      = $startIndex;
        $count    = count($elements);

        while ($idx < $count) {
            $type = $elements[$idx]['type'];

            if ($type === 'bs_gridStart') {
                $depth++;
                $children[] = $elements[$idx];
            } elseif ($type === 'bs_gridStop') {
                if ($depth === 0) {
                    break;
                }

                $depth--;
                $children[] = $elements[$idx];
            } elseif ($type === 'bs_gridSeparator') {
                if ($depth === 0) {
                    break;
                }

                $children[] = $elements[$idx];
            } else {
                $children[] = $elements[$idx];
            }

            $idx++;
        }

        return [$children, $idx];
    }

    /**
     * @param array<int, array<string, mixed>> $elements
     *
     * @throws Exception
     */
    private function moveElementsToParent(array $elements, int $newPid, string $newPtable): void
    {
        $sorting = 128;

        foreach ($elements as $element) {
            $this->connection->executeStatement(
                'UPDATE tl_content SET pid = :pid, ptable = :ptable, sorting = :sorting WHERE id = :id',
                [
                    'pid'     => $newPid,
                    'ptable'  => $newPtable,
                    'sorting' => $sorting,
                    'id'      => $element['id'],
                ],
            );
            $sorting += 128;
        }
    }

    /** @throws Exception */
    private function deleteElement(int $elementId): void
    {
        $this->connection->executeStatement(
            'DELETE FROM tl_content WHERE id = :id',
            ['id' => $elementId],
        );
    }
}
