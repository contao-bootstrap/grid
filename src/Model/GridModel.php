<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Model;

use Contao\Model;

/**
 * @property string|array $sizes
 * @property int|string   $pid
 * @property string       $noGutters
 * @property string       $rowClass
 * @property string       $align
 * @property string       $justify
 */
class GridModel extends Model
{
    /**
     * Table name.
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
    protected static $strTable = 'tl_bs_grid';
}
