<?php

declare(strict_types=1);

namespace ContaoBootstrap\Grid\DependencyInjection;

use Override;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class ContaoBootstrapGridExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    #[Override]
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'contao_bootstrap.grid.enable_wrapper_migration',
            $config['enable_wrapper_migration'],
        );

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config'),
        );

        $loader->load('config.yaml');
        $loader->load('services.yaml');
        $loader->load('listeners.yaml');
    }
}
