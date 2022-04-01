<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Exception;

use RuntimeException;

use function sprintf;

class GridNotFound extends RuntimeException
{
    /**
     * Create the exception with a predefined message.
     *
     * @param int $gridId The grid id.
     */
    public static function withId(int $gridId): self
    {
        return new self(sprintf('Grid with ID "%s" not found', $gridId));
    }
}
