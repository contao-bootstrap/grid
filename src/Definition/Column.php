<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Florian Vick <florian@florian-vick.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Definition;

/**
 * Class Column.
 *
 * @package ContaoBootstrap\Grid\Definition
 *
 * @SuppressWarnings(TooManyPublicMethods)
 */
class Column
{
    /**
     * Column width.
     *
     * @var string
     */
    private $width;

    /**
     * Order setting.
     *
     * @var int
     */
    private $order;

    /**
     * Offset setting.
     *
     * @var string|int
     */
    private $offset;

    /**
     * Align.
     *
     * @var string
     */
    private $align;

    /**
     * Add reset before the column.
     *
     * @var bool
     */
    private $reset = false;

    /**
     * Justify setting.
     *
     * @var string
     */
    private $justify;

    /**
     * Css classes.
     *
     * @var array
     */
    private $cssClasses;

    /**
     * Set the column width.
     *
     * @param int $width Column width.
     *
     * @return $this
     */
    public function width(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Set the flex order.
     *
     * @param int $order Order value.
     *
     * @return $this
     */
    public function order(int $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Set the offset.
     *
     * @param int|string $offset Offset.
     *
     * @return $this
     */
    public function offset($offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Set the align setting.
     *
     * @param string $align Align setting.
     *
     * @return $this
     */
    public function align(string $align): self
    {
        $this->align = $align;

        return $this;
    }

    /**
     * Set the reset flag.
     *
     * @return $this
     */
    public function reset(): self
    {
        $this->reset = true;

        return $this;
    }

    /**
     * Add a css class.
     *
     * @param string $class Css class.
     *
     * @return $this
     */
    public function cssClass(string $class): self
    {
        $this->cssClasses[] = $class;

        return $this;
    }

    /**
     * Build the column definition.
     *
     * @param array  $classes List of classes.
     * @param string $size    Column size.
     *
     * @return array
     */
    public function build(array $classes, string $size = ''): array
    {
        $sizeSuffix = $size ? '-' . $size : $size;

        if ($this->width === null || $this->width > 0) {
            $widthSuffix = ($this->width > 0) ? '-' . $this->width : '';
            $classes[]   = 'col' . $sizeSuffix . $widthSuffix;
        } elseif ($size) {
            $classes[] = 'd-' . $size . '-none';
        } else {
            $classes[] = 'd-none';
        }

        $this->buildAlign($classes, $sizeSuffix);
        $this->buildJustify($classes, $sizeSuffix);
        $this->buildOrder($classes, $sizeSuffix);
        $this->buildOffset($classes, $sizeSuffix);

        if ($this->cssClasses) {
            $classes = array_merge($classes, $this->cssClasses);
        }

        return array_unique($classes);
    }

    /**
     * Build the reset for the column.
     *
     * @param array  $resets Reset definitions.
     * @param string $size   Column size.
     *
     * @return array
     */
    public function buildReset(array $resets, string $size = ''): array
    {
        if ($this->hasReset()) {
            $resets[] = sprintf('d-none d%s-block', $size ? '-' . $size : '');
        }

        return $resets;
    }

    /**
     * Check if reset is required.
     *
     * @return bool
     */
    public function hasReset(): bool
    {
        return $this->reset;
    }

    /**
     * Build the align setting.
     *
     * @param array  $classes    Column classes.
     * @param string $sizeSuffix Bootstrap Size suffix like 'md' or 'lg'.
     *
     * @return void
     */
    private function buildAlign(array &$classes, string $sizeSuffix = ''): void
    {
        if ($this->align) {
            $classes[] = 'align-self'. $sizeSuffix . '-' . $this->align;
        }
    }

    /**
     * Build the justify setting.
     *
     * @param array  $classes    Column classes.
     * @param string $sizeSuffix Bootstrap Size suffix like 'md' or 'lg'.
     *
     * @return void
     */
    private function buildJustify(array &$classes, string $sizeSuffix = ''): void
    {
        if ($this->justify) {
            $classes[] = 'justify-content' . $sizeSuffix . '-' . $this->justify;
        }
    }

    /**
     * Build the order setting.
     *
     * @param array  $classes    Column classes.
     * @param string $sizeSuffix Size suffix.
     *
     * @return void
     */
    private function buildOrder(array &$classes, string $sizeSuffix): void
    {
        if ($this->order) {
            $classes[] = 'order' . $sizeSuffix . '-' . $this->order;
        }
    }

    /**
     * Build offset setting.
     *
     * @param array  $classes    Column classes.
     * @param string $sizeSuffix Size suffix.
     *
     * @return void
     */
    private function buildOffset(array &$classes, string $sizeSuffix): void
    {
        if ($this->offset === null) {
            return;
        }

        if (is_int($this->offset)) {
            $classes[] = 'offset' . $sizeSuffix . '-' . $this->offset;
        } elseif (strlen($this->offset)) {
            $classes[] = $this->offset;
        }
    }
}
