# rekalogika/direct-property-access

Implementation of Symfony's `PropertyAccessorInterface` that reads and writes
directly to the object's properties, bypassing getters and setters.

## Synopsis

```php
use Rekalogika\DirectPropertyAccess\DirectPropertyAccessor;

class Person
{
    private string $name = 'Jane';
}

$propertyAccessor = new DirectPropertyAccessor();

$name = $propertyAccessor->getValue($person, 'name'); // Jane
$propertyAccessor->setValue($person, 'name', 'John');
```

## Documentation

[rekalogika.dev/direct-property-access](https://rekalogika.dev/direct-property-access)

## License

MIT

## Credits

This project took inspiration from the following projects.

* [Symfony Property Access](https://github.com/symfony/property-access)
* [kwn/reflection-property-access](https://github.com/kwn/reflection-property-access)
* [nelmio/alice](https://github.com/nelmio/alice/blob/master/src/PropertyAccess/ReflectionPropertyAccessor.php)

## Contributing

Issues and pull requests should be filed in the GitHub repository
[rekalogika/direct-property-access](https://github.com/rekalogika/direct-property-access).