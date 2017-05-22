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
 * Class Column
 *
 * @package ContaoBootstrap\Grid\Definition
 */
class Column
{
    /**
     * @var string
     */
    private $size;

    /**
     * @var
     */
    private $width;

    /**
     * @var
     */
    private $order;

    /**
     * @var
     */
    private $offset;

    /**
     * @var
     */
    private $align;

    /**
     * @var
     */
    private $reset;

    /**
     * @var
     */
    private $justify;


    public function __construct($size)
    {
        $this->size = $size;
    }

    public function width($width)
    {
        $this->width = $width;

        return $this;
    }

    public function order($flexOrder)
    {
        $this->order = ['flex', $flexOrder];

        return $this;
    }

    public function push($width)
    {
        $this->order = ['push', $width];

        return $this;
    }

    public function pull($width)
    {
        $this->order = ['pull', $width];

        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    public function align($align)
    {
        $this->align = $align;

        return $this;
    }

    public function reset()
    {
        $this->reset = true;

        return $this;
    }

    public function build(array $classes)
    {
        $sizeSuffix  = $this->size ? '-' . $this->size : $this->size;
        $widthSuffix = $this->width ? '-' . $this->width : $this->width;
        $classes[]   = 'col' . $sizeSuffix . $widthSuffix;

        if ($this->align) {
            $classes[] = 'align-self-' . $this->align;
        }

        if ($this->justify) {
            $classes[] = 'justify-content-' . $this->justify;
        }
        
        if ($this->order) {
            if ($this->order[0] === 'flex' || !$this->size) {
                $classes[] = implode('-', $this->order);
            } else {
                $classes[] = sprintf('%s-%s-%s', $this->order[0], $this->size, $this->order[1]);
            }
        }
        
        if ($this->offset) {
            $classes[] = 'offset' . $sizeSuffix . $widthSuffix;
        }
        
        return array_unique($classes);
    }
}
