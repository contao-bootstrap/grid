<?php

declare(strict_types=1);

/**
 * Contao Bootstrap grid.
 *
 * @filesource
 */

use Contao\System;
use ContaoBootstrap\Grid\Migration\MigrateAutoGridWidths;

(static function (): void {
    System::getContainer()->get(MigrateAutoGridWidths::class)();
})();
