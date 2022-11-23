<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Migration;

use ContaoBootstrap\Core\Migration\AbstractGroupWidgetIndexMigration;
use Doctrine\DBAL\Connection;

final class GroupModulesIndexMigration extends AbstractGroupWidgetIndexMigration
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection, 'tl_module', 'bs_gridModules');
    }
}
