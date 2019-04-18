<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2019 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Component\FormField;

use Contao\Widget;
use ContaoBootstrap\Grid\Component\ComponentTrait;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;

/**
 * Class AbstractGridElement.
 *
 * @package ContaoBootstrap\Grid\Component\ContentElement
 */
abstract class AbstractFormField extends Widget
{
    use ComponentTrait;

    /**
     * Get the response tagger service.
     *
     * @return ResponseTagger
     */
    protected function getResponseTagger(): ResponseTagger
    {
        return self::getContainer()->get('contao_bootstrap.grid.response_tagger');
    }
}
