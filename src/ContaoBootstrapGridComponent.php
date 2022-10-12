<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid;

use ContaoBootstrap\Core\ContaoBootstrapComponent;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class ContaoBootstrapGridComponent implements ContaoBootstrapComponent
{
    public function addBootstrapConfiguration(ArrayNodeDefinition $builder): void
    {
        $builder
            ->children()
                ->arrayNode('grid')
                    ->info('Grid component configuration')
                    ->children()
                        ->integerNode('columns')
                            ->info('Default number of columns')
                            ->defaultValue(12)
                        ->end()
                        ->arrayNode('sizes')
                            ->info('Default column sized')
                            ->defaultValue(['xs', 'sm', 'md', 'lg', 'xl', 'xxl'])
                            ->scalarPrototype()->end()
                        ->end()
                        ->scalarNode('default_size')
                            ->info('Default size created without size suffix')
                            ->defaultValue('xs')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
