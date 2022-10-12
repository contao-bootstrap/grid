<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Model\Collection;
use Contao\Theme;
use Contao\ZipWriter;
use ContaoBootstrap\Grid\Model\GridModel;
use DOMDocument;

final class ThemeExportListener extends Theme
{
    /**
     * Contao Framework.
     */
    private ContaoFramework $framework;

    /** @param ContaoFramework $framework Contao framework. */
    public function __construct(ContaoFramework $framework)
    {
        parent::__construct();

        $this->framework = $framework;
    }

    /**
     * Handle the export theme hook.
     *
     * @param DOMDocument $xml     Xml document.
     * @param ZipWriter   $archive Zip archive.
     * @param int|string  $themeId Theme id.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function onExportTheme(DOMDocument $xml, ZipWriter $archive, int|string $themeId): void
    {
        // Add the tables
        $table = $xml->createElement('table');
        $table->setAttribute('name', 'tl_bs_grid');

        $tables = $xml->getElementsByTagName('tables')->item(0);
        $table  = $tables->appendChild($table);

        $adapter    = $this->framework->getAdapter(GridModel::class);
        $collection = $adapter->findBy('pid', $themeId);

        if (! $collection instanceof Collection) {
            return;
        }

        foreach ($collection as $model) {
            $this->addDataRow($xml, $table, $model->row());
        }
    }
}
