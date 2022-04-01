<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Definition;

use function array_merge;
use function array_unique;
use function array_values;
use function is_int;
use function sprintf;
use function strlen;

/**
 * @SuppressWarnings(TooManyPublicMethods)
 */
class Column
{
    /**
     * Column width.
     */
    private int $width;

    /**
     * Order setting.
     */
    private int $order;

    /**
     * Offset setting.
     *
     * @var string|int
     */
    private $offset;

    /**
     * Align.
     */
    private string $align;

    /**
     * Add reset before the column.
     *
     * @var bool|string
     */
    private $reset = false;

    /**
     * Justify setting.
     */
    private string $justify;

    /**
     * Css classes.
     *
     * @var list<string>
     */
    private array $cssClasses = [];

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
     * Force variable width of column.
     *
     * @see https://getbootstrap.com/docs/4.3/layout/grid/#variable-width-content
     *
     * @return Column
     */
    public function variableWidth(): self
    {
        $this->width = 'auto';

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
     * Set the reset flag but limit the reset until a given size.
     *
     * @param string $limit The size to which the reset should be limited.
     *
     * @return Column
     */
    public function limitedReset(string $limit): self
    {
        $this->reset = $limit;

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
     * @param list<string> $classes List of classes.
     * @param string       $size    Column size.
     *
     * @return list<string>
     */
    public function build(array $classes, string $size = ''): array
    {
        $sizeSuffix = $size ? '-' . $size : $size;

        if ($this->width === 'auto') {
            $classes[] = 'col' . $sizeSuffix . '-auto';
        } elseif ($this->width === null || $this->width > 0) {
            $widthSuffix = $this->width > 0 ? '-' . $this->width : '';
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

        return array_values(array_unique($classes));
    }

    /**
     * Build the reset for the column.
     *
     * @param list<string> $resets Reset definitions.
     * @param string       $size   Column size.
     *
     * @return list<string>
     */
    public function buildReset(array $resets, string $size = ''): array
    {
        if ($this->reset === true) {
            $resets[] = sprintf('d-none d%s-block', $size ? '-' . $size : '');
        } elseif ($this->reset !== false) {
            $resets[] = sprintf('d-none d%s-block d-%s-none', $size ? '-' . $size : '', $this->reset);
        }

        return $resets;
    }

    /**
     * Check if reset is required.
     */
    public function hasReset(): bool
    {
        return (bool) $this->reset;
    }

    /**
     * Build the alignment setting.
     *
     * @param list<string> $classes    Column classes.
     * @param string       $sizeSuffix Bootstrap Size suffix like 'md' or 'lg'.
     */
    private function buildAlign(array &$classes, string $sizeSuffix = ''): void
    {
        if (! $this->align) {
            return;
        }

        $classes[] = 'align-self' . $sizeSuffix . '-' . $this->align;
    }

    /**
     * Build the justify setting.
     *
     * @param list<string> $classes    Column classes.
     * @param string       $sizeSuffix Bootstrap Size suffix like 'md' or 'lg'.
     */
    private function buildJustify(array &$classes, string $sizeSuffix = ''): void
    {
        if (! $this->justify) {
            return;
        }

        $classes[] = 'justify-content' . $sizeSuffix . '-' . $this->justify;
    }

    /**
     * Build the order setting.
     *
     * @param list<string> $classes    Column classes.
     * @param string       $sizeSuffix Size suffix.
     */
    private function buildOrder(array &$classes, string $sizeSuffix): void
    {
        if (! $this->order) {
            return;
        }

        $classes[] = 'order' . $sizeSuffix . '-' . $this->order;
    }

    /**
     * Build offset setting.
     *
     * @param list<string> $classes    Column classes.
     * @param string       $sizeSuffix Size suffix.
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
