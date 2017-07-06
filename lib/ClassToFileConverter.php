<?php

namespace Phpactor\ClassFileConverter;

use Phpactor\ClassFileConverter\Adapter\Composer\ComposerFileToClass;
use Phpactor\ClassFileConverter\Adapter\Composer\ComposerClassToFile;
use Phpactor\ClassFileConverter\Domain\ClassToFileFileToClass;
use Phpactor\ClassFileConverter\Domain\FilePath;
use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\ClassFileConverter\Domain\FilePathCandidates;
use Phpactor\ClassFileConverter\Domain\ClassNameCandidates;
use Composer\Autoload\ClassLoader;
use Phpactor\ClassFileConverter\Domain\ChainClassToFile;
use Phpactor\ClassFileConverter\Domain\ChainFileToClass;
use Phpactor\ClassFileConverter\Domain\ClassToFile;
use Phpactor\ClassFileConverter\Domain\FileToClass;

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
