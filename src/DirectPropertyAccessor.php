<?php

namespace Rekalogika\DirectPropertyAccess;

use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
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
