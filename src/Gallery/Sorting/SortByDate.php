<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Gallery\Sorting;

use function uasort;

final class SortByDate implements SortBy
{
    private string $direction;

    private function __construct(string $direction)
    {
        $this->direction = $direction;
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
        uasort(
            $images,
            static fn (array $imageA, array $imageB): int => $direction * $imageA['aux'] <=> $imageB['aux']
        );

        return $images;
    }
}
