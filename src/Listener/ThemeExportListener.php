<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2019 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\Theme;
use Contao\ZipWriter;
use ContaoBootstrap\Grid\Model\GridModel;
use DOMDocument;

/**
 * Class ThemeExportListener.
 */
class ThemeExportListener extends Theme
{
    /**
     * Contao Framework.
     *
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * ThemeExportListener constructor.
     *
     * @param ContaoFrameworkInterface $framework Contao framework.
     */
    public function __construct(ContaoFrameworkInterface $framework)
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
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function onExportTheme(DOMDocument $xml, ZipWriter $archive, $themeId): void
    {
        // Add the tables
        $table = $xml->createElement('table');
        $table->setAttribute('name', 'tl_bs_grid');

        $tables = $xml->getElementsByTagName('tables')->item(0);
        $table  = $tables->appendChild($table);

        /** @var GridModel $adapter */
        $adapter    = $this->framework->getAdapter(GridModel::class);
        $collection = $adapter->findBy('pid', $themeId);

        if ($collection) {
            foreach ($collection as $model) {
                $this->addDataRow($xml, $table, $model->row());
            }
        }
    }
}
