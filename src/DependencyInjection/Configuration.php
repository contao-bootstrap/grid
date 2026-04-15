<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\DependencyInjection;

use Override;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    #[Override]
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('contao_bootstrap_grid');

        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('enable_wrapper_migration')
                    ->defaultFalse()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
