Simple and limited implementation of Symfony's PropertyAccessor that reads and
writes directly to the object's properties, bypassing getters and setters.

# Installation

```bash
composer require rekalogika/domain-event
```

# Usage

In Symfony projects you can autowire `DirectPropertyAccessor`. In other
projects, you can simply instantiate it.

See [Symfony's PropertyAccess
documentation](https://symfony.com/doc/current/components/property_access.html)
for more information.

# Caveats

Currently does not support arrays and paths beyond one level deep.