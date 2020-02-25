<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Florian Vick <fvick@rapid-data.de>
 * @copyright  2017-2020 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\ContentElement;

use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\Database\Result;
use Contao\Environment;
use Contao\File;
use Contao\FilesModel;
use Contao\Input;
use Contao\Model;
use Contao\Model\Collection;
use Contao\Pagination;
use Contao\StringUtil;
use Contao\User;
use ContaoBootstrap\Grid\Exception\GridNotFound;
use ContaoBootstrap\Grid\GridIterator;
use ContaoBootstrap\Grid\GridProvider;
use Netzmacht\Contao\Toolkit\Component\ContentElement\AbstractContentElement;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\View\Template\TemplateReference;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;

/**
 * Class GalleryElement
 */
final class GalleryElement extends AbstractContentElement
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $templateName = 'ce_bs-grid-gallery';

    /**
     * Grid provider.
     *
     * @var GridProvider
     */
    private $gridProvider;

    /**
     * Frontend user.
     *
     * @var User
     */
    private $user;

    /**
     * Images.
     *
     * @var array
     */
    private $images;

    /**
     * Response Tagger.
     *
     * @var ResponseTagger
     */
    private $responseTagger;

    /**
     * Files collection.
     *
     * @var \Contao\Model\Collection|FilesModel
     */
    private $files;

    /**
     * AbstractContentElement constructor.
     *
     * @param Model|Collection|Result $model          Object model or result.
     * @param TemplateEngine          $templateEngine Template engine.
     * @param GridProvider            $gridProvider   Grid provider.
     * @param User                    $user           Contao user.
     * @param ResponseTagger          $responseTagger Response tagger.
     * @param string                  $column         Column.
     */
    public function __construct(
        $model,
        TemplateEngine $templateEngine,
        GridProvider $gridProvider,
        User $user,
        ResponseTagger $responseTagger,
        string $column = 'main'
    ) {
        parent::__construct($model, $templateEngine, $column);

        $this->gridProvider   = $gridProvider;
        $this->user           = $user;
        $this->responseTagger = $responseTagger;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(): string
    {
        if ($this->get('useHomeDir') && defined('FE_USER_LOGGED_IN') && FE_USER_LOGGED_IN) {
            if ($this->user->assignDir && $this->user->homeDir) {
                $this->set('multiSRC', [$this->user->homeDir]);
            }
        } else {
            $this->set('multiSRC', StringUtil::deserialize($this->get('multiSRC'), true));
        }

        // Return if there are no files
        if (!empty($this->get('multiSRC'))) {
            $this->files = FilesModel::findMultipleByUuids($this->get('multiSRC'));
        }

        if (empty($this->files)) {
            return '';
        }

        return parent::generate();
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareTemplateData(array $data): array
    {
        $data = parent::prepareTemplateData($data);

        $auxDate = $this->prepareFiles($this->files);
        $this->applySorting($auxDate, $data);

        // Limit the total number of items (see #2652)
        if ($this->get('numberOfItems') > 0) {
            $this->images = array_slice($this->images, 0, (int) $this->get('numberOfItems'));
        }

        $offset             = 0;
        $limit              = count($this->images);
        $data['pagination'] = $this->preparePagination($offset, $limit);

        $data['images'] = $this->render(
            new TemplateReference(
                $this->getGalleryTemplateName(),
                'html5',
                TemplateReference::SCOPE_FRONTEND
            ),
            array_merge(
                $this->getData(),
                [
                    'body'     => $this->compileImages($offset, $limit),
                    'grid'     => $this->getGridIterator(),
                    'headline' => $this->get('headline'),
                ]
            )
        );

        return $data;
    }

    /**
     * Prepare all file data and return the aux dates.
     *
     * @param Collection $collection File model collection.
     * @param array      $auxDate    Aux date array.
     * @param bool       $deep       If true sub files are added as well.
     *
     * @return array
     *
     * @throws \Exception If file could not be opened.
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
                $file = new File($fileModel->path);

                if (!$file->isImage) {
                    continue;
                }

                // Add the image
                $this->images[$fileModel->path] = [
                    'id'         => $fileModel->id,
                    'uuid'       => $fileModel->uuid,
                    'name'       => $file->basename,
                    'singleSRC'  => $fileModel->path,
                    'title'      => StringUtil::specialchars($file->basename),
                    'filesModel' => $fileModel->current(),
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
     * @param array $data    Template data.
     *
     * @return void
     */
    protected function applySorting(array $auxDate, array &$data): void
    {
        // Sort array
        switch ($this->get('sortBy')) {
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
                $data['isRandomOrder'] = true;
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
     * @return string|null
     *
     * @throws PageNotFoundException When page parameter is out of bounds.
     */
    protected function preparePagination(&$offset, &$limit): ?string
    {
        $total   = count($this->images);
        $perPage = $this->get('perPage');

        // Paginate the result of not randomly sorted (see #8033)
        if ($perPage > 0 && $this->get('sortBy') != 'random') {
            // Get the current page
            $parameter = 'page_g' . $this->get('id');
            $page      = (Input::get($parameter) !== null) ? Input::get($parameter) : 1;

            // Do not index or cache the page if the page number is outside the range
            if ($page < 1 || $page > max(ceil($total / $perPage), 1)) {
                throw new PageNotFoundException('Page not found: ' . Environment::get('uri'));
            }

            // Set limit and offset
            $offset = (($page - 1) * $perPage);
            $limit  = min(($perPage + $offset), $total);

            $pagination = new Pagination(
                $total,
                $perPage,
                Config::get('maxPaginationLinks'),
                $parameter
            );

            return $pagination->generate("\n  ");
        }

        return null;
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
        $lightBoxId = 'lightbox[lb' . $this->get('id') . ']';
        $body       = [];

        $imageSizes = StringUtil::deserialize($this->get('bs_image_sizes'), true);

        for ($index = $offset; $index < $limit; $index++) {
            if (!isset($this->images[$index])) {
                break;
            }

            $cell        = new \stdClass();
            $cell->class = 'image_' . $index;

            // Loop through images sizes.
            $size = current($imageSizes);
            if (next($imageSizes) === false) {
                reset($imageSizes);
            }

            // Build legacy size format.
            if (is_array($size)) {
                $size = [$size['width'], $size['height'], $size['size']];
            }

            // Add size and margin
            $this->images[$index]['size']     = $size;
            $this->images[$index]['fullsize'] = $this->get('fullsize');

            Controller::addImageToTemplate(
                $cell,
                $this->images[$index],
                null,
                $lightBoxId,
                $this->images[$index]['filesModel']
            );

            if ($cell->picture['class']) {
                $cell->picture['class'] = trim($cell->picture['class']);
            }

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
        if (TL_MODE == 'FE' && $this->get('galleryTpl') != '') {
            return (string) $this->get('galleryTpl');
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
        if ($this->get('orderSRC') != '') {
            $tmp = StringUtil::deserialize($this->get('orderSRC'));

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
            if ($this->get('bs_grid')) {
                $iterator = $this->gridProvider->getIterator('ce:' . $this->get('id'), (int) $this->get('bs_grid'));
                $this->responseTagger->addTags(['contao.db.tl_bs_grid.' . $this->get('bs_grid')]);

                return $iterator;
            }
        } catch (GridNotFound $e) {
            // No Grid found, return null.
            return null;
        }

        return null;
    }
}
