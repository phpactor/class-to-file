Class To File Transformer
=========================

[![Build Status](https://travis-ci.org/dtl/composer-inflector.svg?branch=master)](https://travis-ci.org/dtl/composer-inflector)
[![StyleCI](https://styleci.io/repos/<repo-id>/shield)](https://styleci.io/repos/<repo-id>)

This library provides facilities to transform class names to files and
vice-versa.

Use cases:

- **Class generation**: File name from a class name in order to *create a new class file*.
- **Static analysis**: Determine the location of a class in order to
  introspect it.

Usage
-----

```php
// require the composer autoloader for the class you want to investigate
$autoloader = require(__DIR__ . '/vendor/autoload.php');

$classToFile = new ComposerClassToFile($autoloader);
$candidates = $classToFile->classToFileCandidates(ClassName::fromString('Foobar\\Barfoo\\MyClass');

echo (string) $candidates->empty(); // return true if there are no candidates
echo (string) $candidates->first(); // path to the first candidate
echo (string) $candidates->firstExisting(); // path to the first existing file path
```
