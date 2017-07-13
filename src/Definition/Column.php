<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoBootstrap\Grid\Definition;

/**
 * Class Column.
 *
 * @package ContaoBootstrap\Grid\Definition
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
     * @var array
     */
    private $order;

    /**
     * Offset setting.
     *
     * @var string
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
    private $reset;

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
    public function width($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Set the flex order.
     *
     * @param string $flexOrder Flex order setting.
     *
     * @return $this
     */
    public function order($flexOrder)
    {
        $this->order = ['flex', $flexOrder];

        return $this;
    }

    /**
     * Set push order.
     *
     * @param int $width Width.
     *
     * @return $this
     */
    public function push($width)
    {
        $this->order = ['push', $width];

        return $this;
    }

    /**
     * Set pull order.
     *
     * @param int $width Width.
     *
     * @return $this
     */
    public function pull($width)
    {
        $this->order = ['pull', $width];

        return $this;
    }

    /**
     * Set the offset.
     *
     * @param int $offset Offset.
     *
     * @return $this
     */
    public function offset($offset)
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
    public function align($align)
    {
        $this->align = $align;

        return $this;
    }

    /**
     * Set the reset flag.
     *
     * @return $this
     */
    public function reset()
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
    public function cssClass($class)
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
    public function build(array $classes, $size = '')
    {
        $sizeSuffix  = $size ? '-' . $size : $size;
        $widthSuffix = strlen($this->width) ? '-' . $this->width : $this->width;
        $classes[]   = 'col' . $sizeSuffix . $widthSuffix;

        $this->buildAlign($classes);
        $this->buildJustify($classes);
        $this->buildOrder($classes, $size);
        $this->buildOffset($classes, $sizeSuffix, $widthSuffix);

        if ($this->cssClasses) {
            $classes = array_merge($classes, $this->cssClasses);
        }
        
        return array_unique($classes);
    }

    /**
     * Build the align setting.
     *
     * @param array $classes Column classes.
     *
     * @return void
     */
    private function buildAlign(array &$classes)
    {
        if ($this->align) {
            $classes[] = 'align-self-' . $this->align;
        }
    }

    /**
     * Build the justify setting.
     *
     * @param array $classes Column classes.
     *
     * @return void
     */
    private function buildJustify(array &$classes)
    {
        if ($this->justify) {
            $classes[] = 'justify-content-' . $this->justify;
        }
    }

    /**
     * Build the order setting.
     *
     * @param array  $classes Column classes.
     * @param string $size    Device size.
     *
     * @return void
     */
    private function buildOrder(array &$classes, $size)
    {
        if ($this->order) {
            if ($this->order[0] === 'flex' || !$size) {
                $classes[] = implode('-', $this->order);
            } else {
                $classes[] = sprintf('%s-%s-%s', $this->order[0], $size, $this->order[1]);
            }
        }
    }

    /**
     * Build offset setting.
     *
     * @param array  $classes     Column classes.
     * @param string $sizeSuffix  Size suffix.
     * @param string $widthSuffix Width suffix.
     *
     * @return void
     */
    private function buildOffset(array &$classes, $sizeSuffix, $widthSuffix)
    {
        if ($this->offset) {
            $classes[] = 'offset' . $sizeSuffix . $widthSuffix;
        }
    }
}
