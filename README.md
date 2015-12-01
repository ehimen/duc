# Duck Typing in PHP7

Some experimentation with some of the new features in PHP7 and [voodoo magic](http://ocramius.github.io/voodoo-php/#/). For fun, not production!

## DuCollection

A simple collection inspired by [duck typing](https://en.wikipedia.org/wiki/Duck_typing) and [the composite pattern](https://en.wikipedia.org/wiki/Composite_pattern) for PHP7 which proxies
method calls on the collection to the objects it contains.

See [accounts.php](examples/accounts.php) for an example.

## DuClass

Classes wrapped as objects, allowing for repeated instantiations of anonymous classes and multiple inheritance.

See [multiple_inheritance.php](examples/multiple_inheritance.php) for an example.