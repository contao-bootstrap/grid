<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener\Dca;

use Contao\Database\Result;
use Contao\DataContainer;
use Contao\Model;
use stdClass;

use function array_unshift;
use function assert;

/** @template TModel of Model */
abstract class AbstractWrapperDcaListener extends AbstractDcaListener
{
    /**
     * Generate the columns.
     *
     * @param int|string    $value         Number of columns which should be generated.
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return null
     */
    public function generateColumns(int|string $value, DataContainer $dataContainer)
    {
        if (! $dataContainer->activeRecord) {
            return null;
        }

        $current = $dataContainer->activeRecord;
        assert($current instanceof Model || $current instanceof Result || $current instanceof stdClass);
        /** @psalm-var TModel|Result $current */

        if ($value) {
            $stopElement  = $this->getStopElement($current);
            $nextElements = $this->getNextElements($stopElement);
            $sorting      = (int) $stopElement->sorting;

            $sorting = $this->createSeparators((int) $value, $current, $sorting);

            array_unshift($nextElements, $stopElement);
            $this->updateSortings($nextElements, $sorting);
        } else {
            $this->getStopElement($current);
        }

        return null;
    }

    /**
     * Create separators.
     *
     * @param int                          $value   Number of separators being created.
     * @param Model|Result|stdClass        $current Current model.
     * @param int                          $sorting Current sorting value.
     * @psalm-param TModel|Result|stdClass $current
     */
    protected function createSeparators(int $value, Model|Result|stdClass $current, int $sorting): int
    {
        for ($count = 1; $count <= $value; $count++) {
            $sorting += 8;
            $this->createGridElement($current, 'bs_gridSeparator', $sorting);
        }

        return $sorting;
    }

    /**
     * Update the sorting of given elements.
     *
     * @param Model[] $elements    Model collection.
     * @param int     $lastSorting Last sorting value.
     * @psalm-param TModel[] $elements
     */
    protected function updateSortings(array $elements, int $lastSorting): int
    {
        foreach ($elements as $element) {
            if ($lastSorting > $element->sorting) {
                $element->sorting = $lastSorting + 8;
                $element->save();
            }

            $lastSorting = (int) $element->sorting;
        }

        return $lastSorting;
    }

    /**
     * Create the stop element.
     *
     * @param Model|Result|stdClass        $current Model.
     * @param int                          $sorting Last sorting value.
     * @psalm-param TModel|Result|stdClass $current
     *
     * @psalm-return TModel
     */
    protected function createStopElement(Model|Result|stdClass $current, int $sorting): Model
    {
        $sorting += 8;

        return $this->createGridElement($current, 'bs_gridStop', $sorting);
    }

    /**
     * Create a grid element.
     *
     * @param Model|Result|stdClass        $current Current content model.
     * @param string                       $type    Type of the content model.
     * @param int                          $sorting The sorting value.
     * @psalm-param TModel|Result|stdClass $current
     *
     * @return TModel
     */
    abstract protected function createGridElement(Model|Result|stdClass $current, string $type, int &$sorting): Model;

    /**
     * Get the next content elements.
     *
     * @param Model|Result $current Current content model.
     * @psalm-param TModel|Result $current

     * @return Model[]
     * @psalm-return array<TModel>
     */
    abstract protected function getNextElements(Model|Result $current): array;

    /**
     * Get related stop element.
     *
     * @param Model|Result $current Current element.
     * @psalm-param TModel|Result $current
     *
     * @psalm-return TModel
     */
    abstract protected function getStopElement(Model|Result $current): Model;
}
