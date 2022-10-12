<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Gallery\Sorting;

use function array_filter;
use function array_flip;
use function array_key_exists;
use function array_map;
use function array_merge;
use function array_values;

final class SortCustom implements SortBy
{
    /** @param list<string> $orderSrc */
    public function __construct(private readonly array $orderSrc)
    {
    }

    /** {@inheritDoc} */
    public function apply(array $images): array
    {
        if ($this->orderSrc === []) {
            return $images;
        }

        // Remove all values
        $order = array_map(
            static function (int $key): void {
            },
            array_flip($this->orderSrc),
        );

        // Move the matching elements to their position in $arrOrder
        foreach ($images as $k => $v) {
            if (! array_key_exists($v['uuid'], $order)) {
                continue;
            }

            $order[$v['uuid']] = $v;
            unset($images[$k]);
        }

        // Append the left-over images at the end
        if (! empty($this->images)) {
            $order = array_merge($order, array_values($this->images));
        }

        // Remove empty (unreplaced) entries
        return array_filter($order);
    }
}
