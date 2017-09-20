<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Dca;

use Contao\ContentModel;
use Contao\DataContainer;
use Contao\Model;
use ContaoBootstrap\Core\Environment;
use Doctrine\DBAL\Connection;

/**
 * ContentDataContainer helper class.
 *
 * @package ContaoBootstrap\Grid\Dca
 */
class Content extends AbstractWrapperDcaHelper
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
    public function getGridParentOptions(DataContainer $dataContainer = null): array
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
     * Create a grid element.
     *
     * @param ContentModel $current Current content model.
     * @param string       $type    Type of the content model.
     * @param int          $sorting The sorting value.
     *
     * @return Model
     */
    protected function createGridElement($current, string $type, int &$sorting): Model
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
    protected function getNextElements($current): array
    {
        $collection = ContentModel::findBy(
            [
                'tl_content.ptable=?',
                'tl_content.pid=?',
                '(tl_content.type != ? AND tl_content.bs_grid_parent != ?)',
                'tl_content.sorting > ?'
            ],
            [$current->ptable, $current->pid, 'bs_gridStop', $current->id, $current->sorting],
            ['order' => 'tl_content.sorting ASC']
        );

        if ($collection) {
            return $collection->getIterator()->getArrayCopy();
        }

        return [];
    }

    /**
     * Get related stop element.
     *
     * @param ContentModel $current Current element.
     *
     * @return ContentModel|Model
     */
    protected function getStopElement($current): Model
    {
        $stopElement = ContentModel::findOneBy(
            ['tl_content.type=?', 'tl_content.bs_grid_parent=?'],
            ['bs_gridStop', $current->id]
        );

        if ($stopElement) {
            return $stopElement;
        }

        $nextElements = $this->getNextElements($current);
        $stopElement  = $this->createStopElement($current, $current->sorting);
        $this->updateSortings($nextElements, (int) $stopElement->sorting);

        return $stopElement;
    }
}
