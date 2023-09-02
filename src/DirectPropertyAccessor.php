<?php

/*
 * This file is part of rekalogika/direct-property-access package.
 *
 * (c) Priyadi Iman Nurcahyo <https://rekalogika.dev>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Rekalogika\DirectPropertyAccess;

use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\Exception\UninitializedPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * A simple implementation of PropertyAccessorInterface that reads and writes
 * directly to the object's properties. Does not support arrays and multistep
 * paths.
 */
class DirectPropertyAccessor implements PropertyAccessorInterface
{
    /**
     * @param object|array<array-key,mixed> $objectOrArray
     * @phpstan-assert object $objectOrArray
     */
    private function checkOurCapabilities(
        object|array $objectOrArray,
        string|PropertyPathInterface $propertyPath
    ): void {
        if (\is_array($objectOrArray)) {
            throw new InvalidArgumentException('Array is not supported in this implementation of PropertyAccessorInterface');
        }

        if (strpbrk((string) $propertyPath, '.[?')) {
            throw new InvalidArgumentException('Multi-step property path is not supported in this implementation of PropertyAccessorInterface');
        }
    }

    /**
     * @param object|array<array-key,mixed> $objectOrArray
     */
    public function getValue(
        object|array $objectOrArray,
        string|PropertyPathInterface $propertyPath
    ): mixed {
        $this->checkOurCapabilities($objectOrArray, $propertyPath);

        try {
            $reflectionProperty = $this
                ->getReflectionProperty($objectOrArray, $propertyPath);

            return $reflectionProperty->getValue($objectOrArray);
        } catch (\Error $e) {
            // handle uninitialized properties in PHP >= 7.4
            if (preg_match('/^Typed property ([\w\\\\@]+)::\$(\w+) must not be accessed before initialization$/', $e->getMessage(), $matches)) {
                $class = str_contains($matches[1], '@anonymous') ? $objectOrArray::class : $matches[1];
                assert(class_exists($class));
                $r = new \ReflectionProperty($class, $matches[2]);
                $type = ($type = $r->getType()) instanceof \ReflectionNamedType ? $type->getName() : (string) $type;

                throw new UninitializedPropertyException(sprintf('The property "%s::$%s" is not readable because it is typed "%s". You should initialize it or declare a default value instead.', $matches[1], $r->getName(), $type), 0, $e);
            }

            throw $e;
        } catch (\ReflectionException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param object|array<array-key,mixed> $objectOrArray
     */
    public function setValue(
        object|array &$objectOrArray,
        string|PropertyPathInterface $propertyPath,
        mixed $value
    ): void {
        $this->checkOurCapabilities($objectOrArray, $propertyPath);

        try {
            $reflectionProperty = $this
                ->getReflectionProperty($objectOrArray, $propertyPath);

            $reflectionProperty->setValue($objectOrArray, $value);
        } catch (\ReflectionException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param object|array<array-key,mixed> $objectOrArray
     */
    public function isReadable(
        object|array $objectOrArray,
        string|PropertyPathInterface $propertyPath
    ): bool {
        $this->checkOurCapabilities($objectOrArray, $propertyPath);

        return $this->isPresent($objectOrArray, $propertyPath);
    }

    /**
     * @param object|array<array-key,mixed> $objectOrArray
     */
    public function isWritable(
        object|array $objectOrArray,
        string|PropertyPathInterface $propertyPath
    ): bool {
        $this->checkOurCapabilities($objectOrArray, $propertyPath);

        return $this->isPresent($objectOrArray, $propertyPath);
    }

    private function isPresent(
        object $object,
        string|PropertyPathInterface $propertyPath
    ): bool {
        $propertyPath = (string) $propertyPath;

        try {
            $this->getReflectionProperty($object, $propertyPath);

            return true;
        } catch (NoSuchPropertyException $e) {
            return false;
        } catch (\ReflectionException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function getReflectionProperty(
        object $object,
        string|PropertyPathInterface $propertyPath
    ): \ReflectionProperty {
        $propertyPath = (string) $propertyPath;

        $reflectionClass = (new \ReflectionClass(get_class($object)));
        while ($reflectionClass instanceof \ReflectionClass) {
            if (
                $reflectionClass->hasProperty($propertyPath)
                && false === $reflectionClass->getProperty($propertyPath)->isStatic()
            ) {
                $reflectionProperty = $reflectionClass->getProperty($propertyPath);
                $reflectionProperty->setAccessible(true);

                return $reflectionProperty;
            }

            $reflectionClass = $reflectionClass->getParentClass();
        }

        throw new NoSuchPropertyException(sprintf(
            'Property "%s" does not exist in class "%s".',
            $propertyPath,
            get_class($object)
        ));
    }
}
