<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\ContentModel;
use Contao\CoreBundle\Framework\Adapter;
use Contao\DataContainer;
use Contao\Input;
use Contao\Model\Collection;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;

use function array_unique;
use function time;

/**
 * Class ParentFixContentParentRelationsListener fixes the parent relation of grid element for parent tables
 */
final class ParentFixContentParentRelationsListener
{
    /**
     * @param Connection        $connection        Database connection.
     * @param DcaManager        $dcaManager        Data container manager.
     * @param RepositoryManager $repositoryManager Repository manager.
     * @param Adapter<Input>    $inputAdapter      Input adapter.
     */
    public function __construct(
        private readonly Connection $connection,
        private readonly DcaManager $dcaManager,
        private readonly RepositoryManager $repositoryManager,
        private readonly Adapter $inputAdapter,
    ) {
    }

    /**
     * Handle the oncopy_callback.
     *
     * @param string|int    $insertId      Id of new created record.
     * @param DataContainer $dataContainer Data container.
     */
    public function onCopy(string|int $insertId, DataContainer $dataContainer): void
    {
        $this->fixChildRecords((int) $insertId, $dataContainer->table);
    }

    /**
     * Fix record of a table.
     *
     * It checks each record of the child tables and recreates the parent information.
     *
     * @param int    $recordId  The id of the prent record.
     * @param string $tableName The table name of the parent record.
     */
    private function fixChildRecords(int $recordId, string $tableName): void
    {
        $definition  = $this->dcaManager->getDefinition($tableName);
        $childTables = (array) $definition->get(['config', 'ctable'], []);
        $columns     = $this->repositoryManager
            ->getConnection()
            ->createSchemaManager()
            ->listTableColumns($definition->getName());

        /** @psalm-suppress RiskyTruthyFalsyComparison */
        if (
            ! $definition->has(['config', 'ptable'])
            && $this->inputAdapter->get('childs')
            && isset($columns['pid'], $columns['sorting'])
        ) {
            $childTables[] = $definition->getName();
        }

        $schemaManager = $this->repositoryManager->getConnection()->createSchemaManager();

        foreach (array_unique($childTables) as $childTable) {
            if (! $schemaManager->tablesExist([$childTable])) {
                continue;
            }

            if ($childTable === 'tl_content') {
                $this->fixParentRelations($definition->getName(), $recordId);
                continue;
            }

            $childRecords = $this->fetchChildRecordIds($recordId, $definition, $childTable);
            foreach ($childRecords as $childRecordId) {
                $this->fixChildRecords((int) $childRecordId, $childTable);
            }
        }
    }

    /**
     * Fix parent relations for content elements of current parent record.
     *
     * @param string $parentTable The parent table.
     * @param int    $parentId    The parent id.
     */
    private function fixParentRelations(string $parentTable, int $parentId): void
    {
        $collection = $this->loadContentModels($parentTable, $parentId);
        if ($collection === null) {
            return;
        }

        $activeParent = null;
        foreach ($collection as $model) {
            if ($model->type === 'bs_gridStart') {
                $activeParent = $model;
                continue;
            }

            // Broken configuration
            if ($activeParent === null) {
                continue;
            }

            $this->repositoryManager->getConnection()->update(
                ContentModel::getTable(),
                [
                    'bs_grid_parent' => $activeParent->id,
                    'tstamp'         => time(),
                ],
                [
                    'id' => $model->id,
                ],
            );
        }
    }

    /**
     * Load grid content elements which have to be adjusted.
     *
     * @param string $parentTable The parent table.
     * @param int    $parentId    The parent id.
     *
     * @return Collection|ContentModel[]|null
     * @psalm-return Collection|null
     */
    private function loadContentModels(string $parentTable, int $parentId): Collection|null
    {
        $constraints = ['.pid=?', 'FIND_IN_SET( .type, \'bs_gridStart,bs_gridSeparator,bs_gridStop\')'];
        $values      = [$parentId, $parentTable];

        if ($parentTable === 'tl_article') {
            $constraints[] = '( .ptable=? OR .ptable=?)';
            $values[]      = '';
        } else {
            $constraints[] = '.ptable=?';
        }

        return $this->repositoryManager
            ->getRepository(ContentModel::class)
            ->findBy($constraints, $values, ['order' => '.sorting']);
    }

    /**
     * Fetch child record for given definition.
     *
     * @param int        $recordId   The record id.
     * @param Definition $definition The parent definition.
     * @param string     $childTable The child table.
     *
     * @return list<string|int>
     */
    private function fetchChildRecordIds(int $recordId, Definition $definition, string $childTable): array
    {
        $childDefinition = $this->dcaManager->getDefinition($childTable);
        $queryBuilder    = $this->connection->createQueryBuilder()
            ->select('id')
            ->from($childTable)
            ->where('pid=:pid')
            ->setParameter('pid', $recordId);

        if ($childDefinition->get(['config', 'dynamicPtable'])) {
            $queryBuilder
                ->andWhere('ptable=:ptable')
                ->setParameter('ptable', $definition->getName());
        }

        return $queryBuilder->executeQuery()->fetchFirstColumn();
    }
}
