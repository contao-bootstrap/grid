<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Gallery;

use Contao\ContentModel;
use Contao\CoreBundle\Image\Studio\Studio;
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
    /** @param array<array-key,array<string,mixed>> $images */
    public function __construct(
        private readonly Studio $imageStudio,
        private array $images,
        private readonly int $offset,
        private readonly int $limit,
        public Pagination|null $pagination,
    ) {
        $this->images = array_values($images);
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
            /** @psalm-suppress RiskyTruthyFalsyComparison */
            $size = current($imageSizes) ?: null;
            if (next($imageSizes) === false) {
                reset($imageSizes);
            }

            // Build legacy size format.
            if (is_array($size)) {
                $size = [$size['width'], $size['height'], $size['size']];
            }

            $this->imageStudio->createFigureBuilder()
                ->fromFilesModel($this->images[$index]['filesModel'])
                ->setSize($size)
                ->setLightboxGroupIdentifier($lightBoxId)
                ->enableLightbox((bool) $model->fullsize)
                ->setOptions($this->images[$index])
                ->build()
                ->applyLegacyTemplateData($cell);

            if (isset($cell->picture['class']) && $cell->picture['class'] !== '') {
                $cell->picture['class'] = trim($cell->picture['class']);
            }

            $body[] = $cell;
        }

        return $body;
    }
}
