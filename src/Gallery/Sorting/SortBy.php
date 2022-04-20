<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Gallery\Sorting;

interface SortBy
{
    /**
     * @param array<string,array<string,mixed>> $images
     *
     * @return array<string,array<string,mixed>>
     */
    public function apply(array $images): array;
}
