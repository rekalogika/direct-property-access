<?php

/*
 * This file is part of rekalogika/direct-property-access package.
 *
 * (c) Priyadi Iman Nurcahyo <https://rekalogika.dev>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Rekalogika\DirectPropertyAccess\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class RekalogikaDirectPropertyAccessExtension extends Extension
{
    /**
     * @param array<array-key,mixed> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $env = $container->getParameter('kernel.environment');

        $loader = new PhpFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../config')
        );
        
        $loader->load('services.php');

        if ('test' === $env) {
            $loader->load('services_test.php');
        }
    }
}
