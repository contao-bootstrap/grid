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

namespace ContaoBootstrap\Grid\Dca;

use Contao\DataContainer;
use Contao\Model;

/**
 * Class AbstractWrapperDcaHelper.
 *
 * @package ContaoBootstrap\Grid\Dca
 */
abstract class AbstractWrapperDcaHelper extends AbstractDcaHelper
{
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

        if ($value && $dataContainer->activeRecord) {
            $stopElement  = $this->getStopElement($current);
            $nextElements = $this->getNextElements($stopElement);
            $sorting      = $stopElement->sorting;

            $sorting = $this->createSeparators($value, $current, $sorting);

            array_unshift($nextElements, $stopElement);
            $this->updateSortings($nextElements, $sorting);
        }

        return null;
    }

    /**
     * Create separators.
     *
     * @param int   $value   Number of separators being created.
     * @param Model $current Current model.
     * @param int   $sorting Current sorting value.
     *
     * @return int
     */
    protected function createSeparators($value, $current, $sorting)
    {
        for ($count = 1; $count <= $value; $count++) {
            $sorting = ($sorting + 8);
            $this->createGridElement($current, 'bs_gridSeparator', $sorting);
        }

        return $sorting;
    }

    /**
     * Update the sorting of given elements.
     *
     * @param Model[] $elements    Model collection.
     * @param int     $lastSorting Last sorting value.
     *
     * @return int
     */
    protected function updateSortings($elements, $lastSorting)
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
     * Create the stop element.
     *
     * @param Model $current Model.
     * @param int   $sorting Last sorting value.
     *
     * @return Model
     */
    protected function createStopElement($current, $sorting)
    {
        $sorting = ($sorting + 8);

        return $this->createGridElement($current, 'bs_gridStop', $sorting);
    }

    /**
     * Create a grid element.
     *
     * @param Model  $current Current content model.
     * @param string $type    Type of the content model.
     * @param int    $sorting The sorting value.
     *
     * @return Model
     */
    abstract protected function createGridElement($current, $type, &$sorting);

    /**
     * Get the next content elements.
     *
     * @param Model $current Current content model.
     *
     * @return Model[]
     */
    abstract protected function getNextElements($current);

    /**
     * Get related stop element.
     *
     * @param Model $current Current element.
     *
     * @return Model
     */
    abstract protected function getStopElement($current);
}
