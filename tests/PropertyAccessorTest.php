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

use PHPUnit\Framework\TestCase;
use Rekalogika\DirectPropertyAccess\DirectPropertyAccessor;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\Exception\UninitializedPropertyException;

class PropertyAccessorTest extends TestCase
{
    private DirectPropertyAccessor $propertyAccessor;

    public function setUp(): void
    {
        $this->propertyAccessor = new DirectPropertyAccessor();
    }

    /**
     * @testdox Test get/set
     */
    public function testGetSet(): void
    {
        $stub = new Stub();
        $this->propertyAccessor->setValue($stub, 'property', 'value');
        $this->assertSame('value', $this->propertyAccessor->getValue($stub, 'property'));
    }

    /**
     * @testdox Test getting an unset property
     */
    public function testGetUnsetProperty(): void
    {
        $stub = new Stub();
        $this->expectException(UninitializedPropertyException::class);
        $this->propertyAccessor->getValue($stub, 'unsetProperty');
    }

    /**
     * @testdox Test getting nonexistant property
     */
    public function testGetMissingProperty(): void
    {
        $stub = new Stub();
        $this->expectException(NoSuchPropertyException::class);
        $this->propertyAccessor->getValue($stub, 'missingProperty');
    }

    /**
     * @testdox Test setting on nonexistant property
     */
    public function testSetMissingProperty(): void
    {
        $stub = new Stub();
        $this->expectException(NoSuchPropertyException::class);
        $this->propertyAccessor->setValue($stub, 'missingProperty', 'value');
    }
}
