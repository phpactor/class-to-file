<?php

namespace DTL\ClassFileConverter;

use DTL\ClassFileConverter\Adapter\Composer\ComposerFileToClass;
use DTL\ClassFileConverter\Adapter\Composer\ComposerClassToFile;
use DTL\ClassFileConverter\Domain\ClassToFileFileToClass;
use DTL\ClassFileConverter\Domain\FilePath;
use DTL\ClassFileConverter\Domain\ClassName;
use DTL\ClassFileConverter\Domain\FilePathCandidates;
use DTL\ClassFileConverter\Domain\ClassNameCandidates;
use Composer\Autoload\ClassLoader;
use DTL\ClassFileConverter\Domain\ChainClassToFile;
use DTL\ClassFileConverter\Domain\ChainFileToClass;
use DTL\ClassFileConverter\Domain\ClassToFile;
use DTL\ClassFileConverter\Domain\FileToClass;

/**
 * Facade for the library.
 */
final class ClassToFileConverter
{
    private $converter;

    private function __construct(ClassToFileFileToClass $converter)
    {
        $this->converter = $converter;
    }

    public static function fromComposerAutoloader(ClassLoader $classLoader): ClassToFileFileToClass
    {
        return  new ClassToFileFileToClass(
            new ComposerClassToFile($classLoader),
            new ComposerFileToClass($classLoader)
        );
    }

    public static function fromComposerAutoloaders(array $classLoaders): ClassToFileFileToClass
    {
        $classToFiles = $fileToClasses = [];
        foreach ($classLoaders as $classLoader) {
            $classToFiles[] = new ComposerClassToFile($classLoader);
        }
        foreach ($classLoaders as $classLoader) {
            $fileToClasses[] = new ComposerFileToClass($classLoader);
        }

        return new ClassToFileFileToClass(
            new ChainClassToFile($classToFiles),
            new ChainFileToClass($fileToClasses)
        );
    }
}
