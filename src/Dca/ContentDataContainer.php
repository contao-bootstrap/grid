<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoBootstrap\Grid\Dca;

use Contao\ContentModel;
use Contao\DataContainer;
use ContaoBootstrap\Core\Config;
use ContaoBootstrap\Grid\Model\GridModel;
use Doctrine\DBAL\Connection;

/**
 * ContentDataContainer helper class.
 *
 * @package ContaoBootstrap\Grid\Dca
 */
class ContentDataContainer
{
    /**
     * Bootstrap config.
     *
     * @var Config
     */
    private $config;

    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * ContentDataContainer constructor.
     *
     * @param Config     $config     Bootstrap config.
     * @param Connection $connection Database connection.
     */
    public function __construct(Config $config, Connection $connection)
    {
        $this->config     = $config;
        $this->connection = $connection;
    }

    /**
     * Get all available grids.
     *
     * @return array
     */
    public function getGridOptions()
    {
        $collection = GridModel::findAll();
        $options    = [];

        if ($collection) {
            foreach ($collection as $model) {
                $options[$model->id] = sprintf('%s [%s]', $model->title, $model->getRelated('pid')->name);
            }
        }

        return $options;
    }

    /**
     * Get all grid parent options.
     *
     * @param DataContainer|null $dataContainer Data container driver.
     *
     * @return array
     */
    public function getGridParentOptions(DataContainer $dataContainer = null)
    {
        $columns[] = 'tl_content.type = ?';
        $values[]  = 'gridStart';

        if ($dataContainer) {
            $columns[] = 'tl_content.pid = ?';
            $columns[] = 'tl_content.ptable = ?';

            $values[] = $dataContainer->activeRecord->pid;
            $values[] = $dataContainer->activeRecord->ptable;
        }

        $collection = ContentModel::findBy($columns, $values);
        $options    = [];

        if ($collection) {
            foreach ($collection as $model) {
                $options[$model->id] = sprintf(
                    '%s [%s]',
                    $model->bootstrap_grid_name,
                    $model->getRelated('bootstrap_grid')->title
                );
            }
        }

        return $options;
    }

    /**
     * Generate a grid name if not given.
     *
     * @param string        $value         Grid name.
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return string
     */
    public function generateGridName($value, $dataContainer)
    {
        if (!$value) {
            $value = 'grid_' . $dataContainer->activeRecord->id;
        }

        return $value;
    }

    /**
     * Get range of grid columns.
     *
     * @return array
     */
    public function getGridColumns()
    {
        return range (
            1,
            (int) $this->config->get('grid.columns', 12)
        );
    }

    /**
     * Generate the columns.
     *
     * @param int           $value         Number of columns which should be generated.
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return null
     */
    public function generateColumns($value, $dataContainer)
    {
        if (!$value || !$dataContainer->activeRecord) {
            return null;
        }

        $current      = $dataContainer->activeRecord;
        $stopElement  = ContentModel::findOneBy(
            ['tl_content.type=?', 'tl_content.bootstrap_grid_parent=?'],
            ['gridStop', $current->id]
        );

        $nextElements = $this->getNextElements($stopElement ?: $current);
        $sorting      = $stopElement ? $stopElement->sorting : $current->sorting;

        if ($nextElements && $stopElement) {
            $nextElements[] = $stopElement;
        }

        for ($count = 1; $count <= $value; $count++) {
            $sorting = $this->createGridElement($current, 'gridSeparator', ($sorting + 8));
        }

        if (!$stopElement) {
            $sorting = $this->createGridElement($current, 'gridStop', ($sorting + 8));
            $count++;
        }

        if ($count) {
            $this->updateSortings($nextElements, $sorting);
        }

        return null;
    }

    /**
     * Create a grid element.
     *
     * @param ContentModel $current Current content model.
     * @param string       $type    Type of the content model.
     * @param int          $sorting The sorting value.
     *
     * @return int
     */
    private function createGridElement($current, $type, $sorting)
    {
        $model                        = new ContentModel();
        $model->tstamp                = time();
        $model->pid                   = $current->pid;
        $model->ptable                = $current->ptable;
        $model->sorting               = $sorting;
        $model->type                  = $type;
        $model->bootstrap_grid_parent = $current->id;
        $model->save();

        return $sorting;
    }

    /**
     * Get the next content elements.
     *
     * @param ContentModel $current Current content model.
     *
     * @return ContentModel[]
     */
    private function getNextElements($current)
    {
        $collection = ContentModel::findBy(
            ['tl_content.ptable=?', 'tl_content.pid=?', 'sorting > ?'],
            [$current->ptable, $current->pid, $current->sorting],
            ['order' => 'tl_content.sorting ASC']
        );

        if ($collection) {
            return $collection->fetchAll();
        }

        return [];
    }

    /**
     * Update the sorting of given elements.
     *
     * @param ContentModel[] $elements    Content model.
     * @param int            $lastSorting Last sorting value.
     *
     * @return void
     */
    private function updateSortings($elements, $lastSorting)
    {
        if (!$elements) {
            return;
        }

        foreach ($elements as $element) {
            if ($lastSorting > $element->sorting) {
                $element->sorting = ($lastSorting + 8);
                $element->save();
            }

            $lastSorting = $element->sorting;
        }
    }
}
