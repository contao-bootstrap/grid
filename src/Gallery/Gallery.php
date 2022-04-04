<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Gallery;

use Contao\ContentModel;
use Contao\Controller;
use Contao\Pagination;
use Contao\StringUtil;
use stdClass;

use function array_key_exists;
use function array_values;
use function current;
use function is_array;
use function next;
use function reset;
use function trim;

final class Gallery
{
    /** @var list<array<string,mixed>> */
    private array $images;

    private int $offset;

    private int $limit;

    public ?Pagination $pagination;

    /**
     * @param array<array-key,array<string,mixed>> $images
     */
    public function __construct(array $images, int $offset, int $limit, ?Pagination $pagination)
    {
        $this->images     = array_values($images);
        $this->offset     = $offset;
        $this->limit      = $limit;
        $this->pagination = $pagination;
    }

    /** @return list<stdClass> */
    public function compileImages(ContentModel $model): array
    {
        $lightBoxId = 'lightbox[lb' . $model->id . ']';
        $body       = [];

        $imageSizes = StringUtil::deserialize($model->bs_image_sizes, true);

        for ($index = $this->offset; $index < $this->limit; $index++) {
            if (! array_key_exists($index, $this->images)) {
                break;
            }

            $cell        = new stdClass();
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
            /** @psalm-suppress PropertyTypeCoercion - Psalm does not detect that $index is a list key */
            $this->images[$index]['size'] = $size;
            /** @psalm-suppress PropertyTypeCoercion - Psalm does not detect that $index is a list key */
            $this->images[$index]['fullsize'] = $model->fullsize;

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
}
