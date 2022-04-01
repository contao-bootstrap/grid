<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener;

use Contao\ZipReader;
use ContaoBootstrap\Grid\Model\GridModel;
use DOMDocument;
use DOMElement;

class ThemeImportListener
{
    /**
     * Handle the extract theme files hook.
     *
     * @param DOMDocument $xml     Theme xml document.
     * @param ZipReader   $archive Zip archive.
     * @param int|string  $themeId Theme id.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function onExtractThemeFiles(DOMDocument $xml, ZipReader $archive, $themeId): void
    {
        $tables = $xml->getElementsByTagName('table');

        for ($index = 0; $index < $tables->length; $index++) {
            if ($tables->item($index)->getAttribute('name') !== 'tl_bs_grid') {
                continue;
            }

            $this->importGrid($tables->item($index), (int) $themeId);
        }
    }

    /**
     * Import the grid definition.
     *
     * @param DOMElement $item    Table item.
     * @param int        $themeId Theme id.
     */
    private function importGrid(DOMElement $item, int $themeId): void
    {
        $rows = $item->childNodes;

        for ($index = 0; $index < $rows->length; $index++) {
            $values = $this->getRowValues($rows->item($index), $themeId);
            $model  = new GridModel();

            $model->setRow($values);
            $model->save();
        }
    }

    /**
     * Prepare row values.
     *
     * @param DOMElement $item    Row item element.
     * @param int        $themeId Theme id.
     *
     * @return array<string,mixed>
     */
    private function getRowValues(DOMElement $item, int $themeId): array
    {
        $fields = $item->childNodes;
        $values = [];

        for ($index = 0; $index < $fields->length; $index++) {
            $value = $fields->item($index)->nodeValue;
            $name  = $fields->item($index)->getAttribute('name');

            switch ($name) {
                case 'id':
                    break;

                case 'pid':
                    $values[$name] = $themeId;

                    break;

                default:
                    if ($value === 'NULL') {
                        $value = null;
                    }

                    $values[$name] = $value;
            }
        }

        return $values;
    }
}
