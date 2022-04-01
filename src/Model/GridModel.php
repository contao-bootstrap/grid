<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Model;

use Contao\Model;

/**
 * @property string|array sizes
 * @property int          pid
 * @property string       noGutters
 * @property string       rowClass
 * @property string       align
 * @property string       justify
 */
class GridModel extends Model
{
    /**
     * Table name.
     */
    protected static string $strTable = 'tl_bs_grid';
}
