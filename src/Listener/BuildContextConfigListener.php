<?php

/**
 * Contao Bootstrap grid.
 *
 * @package    contao-bootstrap
 * @subpackage Grid
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Patrick Landolt <patrick.landolt@artack.ch>
 * @copyright  2017-2020 netzmacht David Molineus. All rights reserved.
 * @license    https://github.com/contao-bootstrap/grid/blob/master/LICENSE LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener;

use Contao\StringUtil;
use Contao\ThemeModel;
use ContaoBootstrap\Core\Config\ArrayConfig;
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
        if (!$theme instanceof ThemeModel) {
            return;
        }

        $config = $command->getConfig();
        $data   = $config->get([]);
        if ($theme->bs_grid_columns) {
            $data['grid']['columns'] = (int) $theme->bs_grid_columns;
        }
        if ($theme->bs_grid_sizes) {
            $data['grid']['sizes'] = StringUtil::deserialize($theme->bs_grid_sizes, true);
        }
        if ($theme->bs_grid_default_size) {
            $data['grid']['default_size'] = $theme->bs_grid_default_size ?: current($data['grid']['sizes']);
        }

        $command->setConfig(new ArrayConfig($data));
    }
}
