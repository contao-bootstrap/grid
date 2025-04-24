<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Contao\StringUtil;
use ContaoBootstrap\Core\Environment;
use Doctrine\DBAL\Connection;

use function array_merge;
use function array_unique;
use function array_values;
use function is_numeric;
use function serialize;

final class SizeIndexMigration extends AbstractMigration
{
    public function __construct(private readonly Environment $environment, private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();
        if (! $schemaManager->tablesExist(['tl_bs_grid'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_bs_grid');

        foreach ($this->getSizes() as $size) {
            if (! isset($columns[$size . 'size'])) {
                continue;
            }

            $affected = (int) $this->connection->fetchOne(
                'SELECT COUNT(*) FROM tl_bs_grid WHERE ' . $size . 'size LIKE \'a:%:{i:0;%\'',
            );

            if ($affected > 0) {
                return true;
            }
        }

        return false;
    }

    public function run(): MigrationResult
    {
        foreach ($this->getSizes() as $size) {
            $result = $this->connection->fetchAllAssociative(
                'SELECT * FROM tl_bs_grid WHERE ' . $size . 'size LIKE \'a:%:{i:0;%\'',
            );

            foreach ($result as $row) {
                $templates = [];

                foreach (StringUtil::deserialize($row[$size . 'Size'], true) as $key => $template) {
                    if (is_numeric($key)) {
                        ++$key;
                    }

                    $templates[$key] = $template;
                }

                $this->connection->update(
                    'tl_bs_grid',
                    [$size . 'Size' => serialize($templates)],
                    ['id' => $row['id']],
                );
            }
        }

        return $this->createResult(true);
    }

    /**
     * Get all sizes.
     *
     * @return list<string>
     */
    private function getSizes(): array
    {
        $schemaManager = $this->connection->createSchemaManager();
        $columns       = $schemaManager->listTableColumns('tl_theme');

        if (! isset($columns['bs_grid_sizes'])) {
            return [];
        }

        $sizes      = $this->environment->getConfig()->get(['grid', 'sizes'], []);
        $themeSizes = $this->connection->executeQuery('SELECT bs_grid_sizes FROM tl_theme')->fetchFirstColumn();

        foreach ($themeSizes as $themeSize) {
            $sizes = array_merge($sizes, StringUtil::deserialize($themeSize, true));
        }

        return array_values(array_unique($sizes));
    }
}
