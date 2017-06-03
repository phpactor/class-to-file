Class To File Transformer
=========================

[![Build Status](https://travis-ci.org/dantleech/class-to-file.svg?branch=master)](https://travis-ci.org/dantleech/class-to-file)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dantleech/class-to-file/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dantleech/class-to-file/?branch=master)

This library provides facilities to guess/transform class names to files paths
and vice-versa.

Use cases:

- **Class generation**: File name from a class name in order to *create a new class file*.
- **Static analysis**: Determine the location of a class in order to
  introspect it.

Supports:

- **Composer with PSR-0 and PSR-4**

Usage
-----

```php
// require the composer autoloader for the class you want to investigate
$autoloader = require(__DIR__ . '/vendor/autoload.php');

$classToFile = new ComposerClassToFile($autoloader);
$candidates = $classToFile->classToFileCandidates(ClassName::fromString('Foobar\\Barfoo\\MyClass');

echo (string) $candidates->empty(); // return true if there are no candidates
echo (string) $candidates->best(); // path to the "best" candidate
```

TODO
----

- Support class maps
