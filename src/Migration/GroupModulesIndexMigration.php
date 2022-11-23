<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;

use function is_numeric;
use function serialize;

final class GroupModulesIndexMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();
        if (! $schemaManager->tablesExist(['tl_module'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_module');
        if (! isset($columns['bs_gridModules'])) {
            return false;
        }

        $affected = (int) $this->connection->fetchOne(
            'SELECT COUNT(*) FROM tl_module WHERE bs_gridModules LIKE \'a:1:{i:0;%\'',
        );

        return $affected > 0;
    }

    public function run(): MigrationResult
    {
        $result = $this->connection->fetchAllAssociative(
            'SELECT * FROM tl_module WHERE bs_gridModules LIKE \'a:1:{i:0;%\'',
        );

        foreach ($result as $row) {
            $templates = [];

            foreach (StringUtil::deserialize($row['bs_gridModules'], true) as $key => $template) {
                if (is_numeric($key)) {
                    ++$key;
                }

                $templates[$key] = $template;
            }

            $this->connection->update(
                'tl_module',
                ['bs_gridModules' => serialize($templates)],
                ['id' => $row['id']],
            );
        }

        return $this->createResult(true);
    }
}
