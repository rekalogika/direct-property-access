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

class Stub
{
    private string $property;  // @phpstan-ignore-line

    public function getProperty(): string
    {
        throw new \BadMethodCallException('This method should not be called.');
    }

    public function setProperty(string $property): self
    {
        throw new \BadMethodCallException('This method should not be called.');
    }
}
