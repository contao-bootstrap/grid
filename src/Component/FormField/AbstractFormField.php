<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\FormField;

use Contao\Widget;
use ContaoBootstrap\Grid\Component\ComponentTrait;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;

abstract class AbstractFormField extends Widget
{
    use ComponentTrait;

    /**
     * Get the response tagger service.
     */
    protected function getResponseTagger(): ResponseTagger
    {
        return self::getContainer()->get('contao_bootstrap.grid.response_tagger');
    }
}
