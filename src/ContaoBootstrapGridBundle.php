<?php

/**
 * Contao Bootstrap grid.
 *
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Grid;

use ContaoBootstrap\Core\DependencyInjection\ContaoBootstrapCoreExtension;
use Override;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use function assert;

final class ContaoBootstrapGridBundle extends Bundle
{
    #[Override]
    public function build(ContainerBuilder $container): void
    {
        $extension = $container->getExtension('contao_bootstrap');
        assert($extension instanceof ContaoBootstrapCoreExtension);
        $extension->addComponent(new ContaoBootstrapGridComponent());
    }
}
