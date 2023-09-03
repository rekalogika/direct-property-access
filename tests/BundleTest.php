<?php

/*
 * This file is part of rekalogika/reconstitutor package.
 *
 * (c) Priyadi Iman Nurcahyo <https://rekalogika.dev>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Rekalogika\DirectPropertyAccess\Tests;

use PHPUnit\Framework\TestCase;
use Rekalogika\DirectPropertyAccess\DirectPropertyAccessor;

class BundleTest extends TestCase
{
    public function testServiceWiring(): void
    {
        $kernel = new DirectPropertyAccessorKernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();

        $directPropertyAccessor = $container
            ->get('test.' . DirectPropertyAccessor::class);

        $this->assertInstanceOf(
            DirectPropertyAccessor::class,
            $directPropertyAccessor
        );
    }
}
