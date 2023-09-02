Simple and limited implementation of Symfony's `PropertyAccessorInterface` that
reads and writes directly to the object's properties, bypassing getters and
setters.

Installation
------------

```bash
composer require rekalogika/direct-property-access
```

Usage
-----

In Symfony projects you can autowire `DirectPropertyAccessor`. In other
projects, you can simply instantiate it.

See [Symfony's PropertyAccess
documentation](https://symfony.com/doc/current/components/property_access.html)
for more information on how to use it. The difference is that
`DirectPropertyAccessor` does not call any of the object's methods, but reads
and writes directly to the object's properties, even if they are private.

Caveats
-------

Currently does not support arrays and paths beyond one level deep.