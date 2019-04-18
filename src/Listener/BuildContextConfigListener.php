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

namespace ContaoBootstrap\Grid\Listener;

use Contao\ThemeModel;
use ContaoBootstrap\Core\Environment\ThemeContext;
use ContaoBootstrap\Core\Message\Command\BuildContextConfig;

/**
 * Class BuildContextConfigListener.
 *
 * @package ContaoBootstrap\Grid\Message\Subscriber
 */
class BuildContextConfigListener
{
    /**
     * Build theme config.
     *
     * @param BuildContextConfig $command Command.
     *
     * @return void
     */
    public function buildThemeConfig(BuildContextConfig $command): void
    {
        $context = $command->getContext();

        if (!$context instanceof ThemeContext) {
            return;
        }

        $theme = ThemeModel::findByPk($context->getThemeId());

        if ($theme && $theme->bs_grid_columns) {
            $config = $command->getConfig()->merge(
                [
                    'grid' => [
                        'columns' => (int) $theme->bs_grid_columns
                    ]
                ]
            );

            $command->setConfig($config);
        }
    }
}
