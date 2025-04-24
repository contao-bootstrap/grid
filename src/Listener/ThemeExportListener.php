<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Model\Collection;
use Contao\Theme;
use Contao\ZipWriter;
use ContaoBootstrap\Grid\Model\GridModel;
use DOMDocument;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;

final class ThemeExportListener extends Theme
{
    public function __construct(private readonly RepositoryManager $repositories)
    {
        parent::__construct();
    }

    /**
     * Handle the export theme hook.
     *
     * @param DOMDocument $xml     Xml document.
     * @param ZipWriter   $archive Zip archive.
     * @param int|string  $themeId Theme id.
     *
     * @Hook("exportTheme")
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function onExportTheme(DOMDocument $xml, ZipWriter $archive, int|string $themeId): void
    {
        // Add the tables
        $table = $xml->createElement('table');
        $table->setAttribute('name', 'tl_bs_grid');

        $tables = $xml->getElementsByTagName('tables')->item(0);
        $table  = $tables->appendChild($table);

        $adapter    = $this->repositories->getRepository(GridModel::class);
        $collection = $adapter->findBy(['.pid=?'], [$themeId]);

        if (! $collection instanceof Collection) {
            return;
        }

        foreach ($collection as $model) {
            $this->addDataRow($xml, $table, $model->row());
        }
    }
}
