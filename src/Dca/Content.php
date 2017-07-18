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
use ContaoBootstrap\Core\Environment;
use Doctrine\DBAL\Connection;

/**
 * ContentDataContainer helper class.
 *
 * @package ContaoBootstrap\Grid\Dca
 */
class Content extends AbstractDcaHelper
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * ContentDataContainer constructor.
     *
     * @param Environment $environment Bootstrap environment.
     * @param Connection  $connection  Database connection.
     */
    public function __construct(Environment $environment, Connection $connection)
    {
        parent::__construct($environment);

        $this->connection = $connection;
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
        $values[]  = 'bs_gridStart';

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
                    $model->bs_grid_name,
                    $model->getRelated('bs_grid')->title
                );
            }
        }

        return $options;
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
        if (!$dataContainer->activeRecord) {
            return null;
        }

        $current = $dataContainer->activeRecord;
        $this->getStopElement($current);

        if ($value && $dataContainer->activeRecord) {
            $nextElements = $this->getNextElements($current);
            $sorting      = $current->sorting;

            $sorting = $this->createSeparators($value, $current, $sorting);

            if ($value) {
                $this->updateSortings($nextElements, $sorting);
            }
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
     * @return ContentModel
     */
    private function createGridElement($current, $type, &$sorting)
    {
        $model                 = new ContentModel();
        $model->tstamp         = time();
        $model->pid            = $current->pid;
        $model->ptable         = $current->ptable;
        $model->sorting        = $sorting;
        $model->type           = $type;
        $model->bs_grid_parent = $current->id;
        $model->save();

        return $model;
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
            return $collection->getIterator()->getArrayCopy();
        }

        return [];
    }

    /**
     * Update the sorting of given elements.
     *
     * @param ContentModel[] $elements    Content model.
     * @param int            $lastSorting Last sorting value.
     *
     * @return int
     */
    private function updateSortings($elements, $lastSorting)
    {
        foreach ($elements as $element) {
            if ($lastSorting > $element->sorting) {
                $element->sorting = ($lastSorting + 8);
                $element->save();
            }

            $lastSorting = $element->sorting;
        }

        return $lastSorting;
    }

    /**
     * Get related stop element.
     *
     * @param ContentModel $current Current element.
     *
     * @return ContentModel|null
     */
    private function getStopElement($current)
    {
        $stopElement = ContentModel::findOneBy(
            ['tl_content.type=?', 'tl_content.bs_grid_parent=?'],
            ['bs_gridStop', $current->id]
        );

        if ($stopElement) {
            return $stopElement;
        }

        return $this->createStopElement($current, $current->sorting);
    }

    /**
     * Create the stop element.
     *
     * @param ContentModel $current Content model.
     * @param int          $sorting Last sorting value.
     *
     * @return ContentModel
     */
    private function createStopElement($current, $sorting)
    {
        $sorting = ($sorting + 8);

        return $this->createGridElement($current, 'bs_gridStop', $sorting);
    }

    /**
     * Create separators.
     *
     * @param int          $value   Number of separators being created.
     * @param ContentModel $current Content model.
     * @param int          $sorting Current sorting value.
     *
     * @return int
     */
    private function createSeparators($value, $current, $sorting)
    {
        for ($count = 1; $count <= $value; $count++) {
            $sorting = ($sorting + 8);
            $this->createGridElement($current, 'bs_gridSeparator', $sorting);
        }

        return $sorting;
    }
}
