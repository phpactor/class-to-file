Class To File Transformer
=========================

![CI](https://github.com/phpactor/class-to-file/workflows/CI/badge.svg)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phpactor/class-to-file/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phpactor/class-to-file/?branch=master)

This library provides facilities to guess/transform class names to files paths
and vice-versa.

It uses the composer autoload to guess the best candidates using **PSR-0** and
**PSR-4**.

Usage
-----

```php
// require the composer autoloader for the class you want to investigate
$autoloader = require(__DIR__ . '/vendor/autoload.php');

// for file candidates
$converter = ClassFileConverter::fromComposerAutoloader($autoloader);
$candidates = $classToFile->classToFileCandidates(ClassName::fromString('Foobar\\Barfoo\\MyClass'));

echo (string) $candidates->empty(); // return true if there are no candidates
echo (string) $candidates->best(); // path to the "best" candidate

// or for class candidates
$candidates = $classToFile->fileToClassCandidates(FilePath::fromString('lib/Foobar/BarFoo/MyClass.php'));
```

Doesn't composer provide file paths for classes?
------------------------------------------------

Yes it does, but only if the class **exists**. One of the purposes of this
class is to be able to determine a file path for a class which may not exist
(for example to generate a class in a new file).

You may also want to do this and not pollute your autoloading environment
(unfortunately this library currently [does pollute the
autoloader](https://github.com/dantleech/class-to-file/issues/3), but it can
be avoided in the future.

Why would you want to determine the class from a filename?
----------------------------------------------------------

Glad you asked! This is can be useful when you want to generate
an empty class in an empty file.

When one autoloader isn't enough
--------------------------------

In some exceptional cases you may have a project which has more than one
composer autoloader, this is supported through the `ChainFileToClass` and
`ChainClassToFile` classes, or most simply through the *facade*:

```php
$converter = ClassFileConverter::fromComposerAutoloaders([ $autoloader1, $autoloader2 ]);
```

Contributing
------------

This package is open source and welcomes contributions! Feel free to open a
pull request on this repository.

Support
-------

- Create an issue on the main [Phpactor](https://github.com/phpactor/phpactor) repository.
- Join the `#phpactor` channel on the Slack [Symfony Devs](https://symfony.com/slack-invite) channel.

