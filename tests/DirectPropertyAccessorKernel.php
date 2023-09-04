<?php

/*
 * This file is part of rekalogika/direct-property-access package.
 *
 * (c) Priyadi Iman Nurcahyo <https://rekalogika.dev>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Rekalogika\DirectPropertyAccess\Tests;

use Rekalogika\DirectPropertyAccess\RekalogikaDirectPropertyAccessBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class DirectPropertyAccessorKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new RekalogikaDirectPropertyAccessBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }
}
