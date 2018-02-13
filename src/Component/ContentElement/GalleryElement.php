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

namespace ContaoBootstrap\Grid\Component\ContentElement;

use Contao\Config;
use Contao\ContentGallery;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\Model\Collection;
use Contao\Pagination;
use Contao\StringUtil;
use ContaoBootstrap\Grid\GridIterator;
use ContaoBootstrap\Grid\GridProvider;

/**
 * Class GalleryElement.
 *
 * @property string bs_grid        Bootstrap grid id.
 * @property mixed  bs_image_sizes Image sizes.
 */
class GalleryElement extends ContentGallery
{
    /**
     * Images.
     *
     * @var array
     */
    private $images;

    /**
     * {@inheritdoc}
     */
    protected function compile()
    {
        $this->images = [];

        $auxDate = $this->prepareFiles($this->objFiles);
        $this->applySorting($auxDate);

        // Limit the total number of items (see #2652)
        if ($this->numberOfItems > 0) {
            $this->images = array_slice($this->images, 0, (int) $this->numberOfItems);
        }

        $offset = 0;
        $limit  = count($this->images);

        $this->preparePagination($offset, $limit);

        $template = new FrontendTemplate($this->getGalleryTemplateName());
        $template->setData($this->arrData);

        $template->body = $this->compileImages($offset, $limit);
        $template->grid = $this->getGridIterator();

        // see contao/core #1603
        $template->headline = $this->headline;

        $this->Template->images = $template->parse();
    }

    /**
     * Prepare all file data and return the aux dates.
     *
     * @param Collection $collection File model collection.
     * @param array      $auxDate    Aux date array.
     * @param bool       $deep       If true sub files are added as well.
     *
     * @return array
     */
    protected function prepareFiles(Collection $collection, array $auxDate = [], $deep = true): array
    {
        // Get all images
        foreach ($collection as $fileModel) {
            // Continue if the files has been processed or does not exist
            if (isset($this->images[$fileModel->path]) || !file_exists(TL_ROOT . '/' . $fileModel->path)) {
                continue;
            }

            if ($fileModel->type == 'file') {
                // Single files
                $file = new \File($fileModel->path);

                if (!$file->isImage) {
                    continue;
                }

                // Add the image
                $this->images[$fileModel->path] = [
                    'id'         => $fileModel->id,
                    'uuid'       => $fileModel->uuid,
                    'name'       => $file->basename,
                    'singleSRC'  => $fileModel->path,
                    'title'      => \StringUtil::specialchars($file->basename),
                    'filesModel' => $fileModel->current()
                ];

                $auxDate[] = $file->mtime;
            } elseif ($deep) {
                // Folders
                $subfiles = FilesModel::findByPid($fileModel->uuid);

                if ($subfiles !== null) {
                    $this->prepareFiles($subfiles, $auxDate, false);
                }
            }
        }

        return $auxDate;
    }

    /**
     * Apply the sorting.
     *
     * @param array $auxDate Aux dates.
     *
     * @return void
     */
    protected function applySorting(array $auxDate): void
    {
        // Sort array
        switch ($this->sortBy) {
            default:
            case 'name_asc':
                uksort($this->images, 'basename_natcasecmp');
                break;

            case 'name_desc':
                uksort($this->images, 'basename_natcasercmp');
                break;

            case 'date_asc':
                array_multisort($this->images, SORT_NUMERIC, $auxDate, SORT_ASC);
                break;

            case 'date_desc':
                array_multisort($this->images, SORT_NUMERIC, $auxDate, SORT_DESC);
                break;

            // Deprecated since Contao 4.0, to be removed in Contao 5.0
            case 'meta':
                // @codingStandardsIgnoreStart
                @trigger_error(
                    'The "meta" key in ContentGallery::compile() has been deprecated and will no longer work in Contao 5.0.',
                    E_USER_DEPRECATED
                );
                // @codingStandardsIgnoreEnd

            // no break here. Handle meta the same as custom.
            case 'custom':
                $this->applyCustomSorting();
                break;

            case 'random':
                shuffle($this->images);
                $this->Template->isRandomOrder = true;
                break;
        }

        $this->images = array_values($this->images);
    }

    /**
     * Prepare pagination.
     *
     * @param int $offset Offset number.
     * @param int $limit  Limit.
     *
     * @return void
     *
     * @throws PageNotFoundException When page parameter is out of bounds.
     */
    protected function preparePagination(&$offset, &$limit): void
    {
        $total = count($this->images);

        // Paginate the result of not randomly sorted (see #8033)
        if ($this->perPage > 0 && $this->sortBy != 'random') {
            // Get the current page
            $parameter = 'page_g' . $this->id;
            $page      = (\Input::get($parameter) !== null) ? \Input::get($parameter) : 1;

            // Do not index or cache the page if the page number is outside the range
            if ($page < 1 || $page > max(ceil($total / $this->perPage), 1)) {
                throw new PageNotFoundException('Page not found: ' . \Environment::get('uri'));
            }

            // Set limit and offset
            $offset = (($page - 1) * $this->perPage);
            $limit  = min(($this->perPage + $offset), $total);

            $pagination = new Pagination(
                $total,
                $this->perPage,
                Config::get('maxPaginationLinks'),
                $parameter
            );

            $this->Template->pagination = $pagination->generate("\n  ");
        }
    }

    /**
     * Compile all images.
     *
     * @param int $offset Offset.
     * @param int $limit  Limit.
     *
     * @return array
     */
    protected function compileImages($offset, $limit): array
    {
        $lightBoxId = 'lightbox[lb' . $this->id . ']';
        $body       = [];

        $imageSizes = StringUtil::deserialize($this->bs_image_sizes, true);

        for ($index = $offset; $index < $limit; $index++) {
            if (!isset($this->images[$index])) {
                break;
            }

            $cell        = new \stdClass();
            $cell->class = 'image_' . $index;

            // Loop through images sizes.
            $size = current($imageSizes);
            $key  = key($imageSizes);

            if (!array_key_exists('repeat', $size) && next($imageSizes) === false) {
                reset($imageSizes);
            } elseif (array_key_exists('repeat', $imageSizes[$key])) {
                if ($imageSizes[$key] === '') {
                    $imageSizes[$key] = 1;
                }

                if ($imageSizes[$key]['repeat'] > 1) {
                    $imageSizes[$key]['repeat']--;
                } elseif (next($imageSizes) === false) {
                    reset($imageSizes);
                }
            }

            // Build legacy size format.
            if (is_array($size)) {
                $size = [$size['width'], $size['height'], $size['size']];
            }

            // Add size and margin
            $this->images[$index]['size']     = $size;
            $this->images[$index]['fullsize'] = $this->fullsize;

            $this->addImageToTemplate(
                $cell,
                $this->images[$index],
                null,
                $lightBoxId,
                $this->images[$index]['filesModel']
            );

            $cell->picture['attributes'] = 'class="img-fluid figure-img"';

            $body[] = $cell;
        }

        return $body;
    }

    /**
     * Get the gallery template name.
     *
     * @return string
     */
    protected function getGalleryTemplateName(): string
    {
        $templateName = 'bs_gallery_default';

        // Use a custom template
        if (TL_MODE == 'FE' && $this->galleryTpl != '') {
            $templateName = $this->galleryTpl;
        }

        return $templateName;
    }

    /**
     * Apply custom sorting.
     *
     * @return void
     */
    protected function applyCustomSorting(): void
    {
        if ($this->orderSRC != '') {
            $tmp = \StringUtil::deserialize($this->orderSRC);

            if (!empty($tmp) && is_array($tmp)) {
                // Remove all values
                $order = array_map(
                    function () {
                    },
                    array_flip($tmp)
                );

                // Move the matching elements to their position in $arrOrder
                foreach ($this->images as $k => $v) {
                    if (array_key_exists($v['uuid'], $order)) {
                        $order[$v['uuid']] = $v;
                        unset($this->images[$k]);
                    }
                }

                // Append the left-over images at the end
                if (!empty($this->images)) {
                    $order = array_merge($order, array_values($this->images));
                }

                // Remove empty (unreplaced) entries
                $this->images = array_values(array_filter($order));
                unset($order);
            }
        }
    }

    /**
     * Get the grid iterator.
     *
     * @return GridIterator|null
     */
    private function getGridIterator(): ?GridIterator
    {
        try {
            if ($this->bs_grid) {
                $provider = $this->getGridProvider();

                return $provider->getIterator('ce:' . $this->id, (int) $this->bs_grid);
            }
        } catch (\RuntimeException $e) {
            // No Grid found, return null.
        }

        return null;
    }
    /**
     * Get the grid provider.
     *
     * @return GridProvider
     */
    private function getGridProvider(): GridProvider
    {
        return static::getContainer()->get('contao_bootstrap.grid.grid_provider');
    }
}
