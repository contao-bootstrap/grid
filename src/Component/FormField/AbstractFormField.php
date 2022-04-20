<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\FormField;

use Contao\Widget;
use ContaoBootstrap\Grid\Component\ComponentTrait;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;

/** @psalm-suppress DeprecatedTrait */
abstract class AbstractFormField extends Widget
{
    use ComponentTrait;

    /**
     * Get the response tagger service.
     *
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress InvalidNullableReturnType
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress NullableReturnStatement
     */
    protected function getResponseTagger(): ResponseTagger
    {
        return self::getContainer()->get('contao_bootstrap.grid.response_tagger');
    }
}
