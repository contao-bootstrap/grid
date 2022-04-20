<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Gallery\Sorting;

use function array_keys;
use function shuffle;

final class SortRandom implements SortBy
{
    /** {@inheritDoc} */
    public function apply(array $images): array
    {
        $keys = array_keys($images);
        $new  = [];

        shuffle($keys);

        foreach ($keys as $key) {
            $new[$key] = $images[$key];
        }

        return $new;
    }
}
