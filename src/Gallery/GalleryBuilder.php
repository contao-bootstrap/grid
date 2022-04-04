<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Gallery;

use Contao\Config;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Environment;
use Contao\File;
use Contao\FilesModel;
use Contao\Input;
use Contao\Model\Collection;
use Contao\Pagination;
use Contao\StringUtil;
use ContaoBootstrap\Grid\Gallery\Sorting\SortBy;
use ContaoBootstrap\Grid\Gallery\Sorting\SortRandom;

use function array_slice;
use function assert;
use function ceil;
use function count;
use function file_exists;
use function is_string;
use function max;
use function min;

final class GalleryBuilder
{
    private ContaoFramework $framework;

    private string $projectDir;

    /** @var array<string,array<string,mixed>> */
    private array $images = [];

    private ?SortBy $sortBy = null;

    private int $limit = 0;

    private int $perPage = 0;

    private ?string $pageParam = null;

    public function __construct(ContaoFramework $framework, string $projectDir)
    {
        $this->framework  = $framework;
        $this->projectDir = $projectDir;
    }

    public function sortBy(SortBy $sortBy): self
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function perPage(string $pageParam, int $perPage): self
    {
        $this->pageParam = $pageParam;
        $this->perPage   = $perPage;

        return $this;
    }

    /** @param list<string> $uuids */
    public function addSources(array $uuids): self
    {
        $this->framework->initialize();

        $repository = $this->framework->getAdapter(FilesModel::class);
        $collection = $repository->findMultipleByUuids($uuids);
        if ($collection instanceof Collection) {
            $this->loadImages($collection);
        }

        return $this;
    }

    public function build(): Gallery
    {
        $images     = $this->applySorting($this->images);
        $images     = $this->applyLimit($images);
        $offset     = 0;
        $limit      = $this->limit;
        $pagination = $this->preparePagination($offset, $limit);

        return new Gallery($images, $offset, $limit, $pagination);
    }

    private function loadImages(Collection $collection, bool $deep = true): void
    {
        // Get all images
        foreach ($collection as $fileModel) {
            // Continue if the files has been processed or does not exist
            if (
                isset($this->images[$fileModel->path])
                || ! file_exists($this->projectDir . '/' . $fileModel->path)
            ) {
                continue;
            }

            if ($fileModel->type === 'file') {
                // Single files
                $file = new File($fileModel->path);
                if (! $file->isImage) {
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
                    'aux'        => $file->mtime,
                ];
            } elseif ($deep) {
                // Folders
                $repository = $this->framework->getAdapter(FilesModel::class);
                $children   = $repository->findByPid($fileModel->uuid);

                if ($children instanceof Collection) {
                    $this->loadImages($children, false);
                }
            }
        }
    }

    /**
     * @param array<string,array<string,mixed>> $images
     *
     * @return array<string,array<string,mixed>>
     */
    protected function applySorting(array $images): array
    {
        if ($this->sortBy) {
            return $this->sortBy->apply($images);
        }

        return $images;
    }

    /**
     * @param array<string,array<string,mixed>> $images
     *
     * @return array<string,array<string,mixed>>
     */
    private function applyLimit(array $images): array
    {
        if ($this->limit > 0) {
            return array_slice($images, 0, $this->limit);
        }

        return $images;
    }

    /**
     * Prepare pagination.
     *
     * @param int $offset Offset number.
     * @param int $limit  Limit.
     *
     * @throws PageNotFoundException When page parameter is out of bounds.
     */
    protected function preparePagination(int &$offset, int &$limit): ?Pagination
    {
        $total   = count($this->images);
        $perPage = $this->perPage;

        // Paginate the result of not randomly sorted (see #8033)
        if ($perPage > 0 && ! $this->sortBy instanceof SortRandom) {
            assert(is_string($this->pageParam));

            // Get the current page
            $parameter = $this->pageParam;
            $page      = Input::get($parameter) ?? 1;

            // Do not index or cache the page if the page number is outside the range
            if ($page < 1 || $page > max(ceil($total / $perPage), 1)) {
                throw new PageNotFoundException('Page not found: ' . Environment::get('uri'));
            }

            // Set limit and offset
            $offset = ($page - 1) * $perPage;
            $limit  = min($perPage + $offset, $total);

            return new Pagination(
                $total,
                $perPage,
                Config::get('maxPaginationLinks'),
                $parameter
            );
        }

        return null;
    }
}
