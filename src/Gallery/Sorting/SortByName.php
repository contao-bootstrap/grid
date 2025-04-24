<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Gallery\Sorting;

use function basename;
use function strnatcasecmp;
use function uksort;

final class SortByName implements SortBy
{
    private function __construct(private readonly string $direction)
    {
    }

    public static function asc(): self
    {
        return new self('asc');
    }

    public static function desc(): self
    {
        return new self('desc');
    }

    /** {@inheritDoc} */
    public function apply(array $images): array
    {
        $direction = $this->direction === 'asc' ? 1 : -1;
        uksort(
            $images,
            static function (string $imageA, string $imageB) use ($direction): int {
                return $direction * strnatcasecmp(basename($imageA), basename($imageB));
            },
        );

        return $images;
    }
}
