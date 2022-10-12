<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\Listener;

use Contao\StringUtil;
use Contao\ThemeModel;
use ContaoBootstrap\Core\Config\ArrayConfig;
use ContaoBootstrap\Core\Environment\ThemeContext;
use ContaoBootstrap\Core\Message\Command\BuildContextConfig;

use function array_filter;
use function array_values;
use function current;

final class BuildContextConfigListener
{
    /**
     * Build theme config.
     *
     * @param BuildContextConfig $command Command.
     */
    public function buildThemeConfig(BuildContextConfig $command): void
    {
        $context = $command->context;

        if (! $context instanceof ThemeContext) {
            return;
        }

        $theme = ThemeModel::findByPk($context->themeId);
        if (! $theme instanceof ThemeModel) {
            return;
        }

        $config = $command->config;
        $data   = $config->get([]);
        if ($theme->bs_grid_columns) {
            $data['grid']['columns'] = (int) $theme->bs_grid_columns;
        }

        $sizes = array_filter(StringUtil::deserialize($theme->bs_grid_sizes, true));
        if ($sizes !== []) {
            $data['grid']['sizes'] = array_values($sizes);
        }

        $data['grid']['default_size'] = $theme->bs_grid_default_size ?: current($data['grid']['sizes']);

        $command->config = new ArrayConfig($data);
    }
}
