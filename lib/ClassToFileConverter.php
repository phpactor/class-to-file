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

    public static function fromComposerAutoloader(ClassLoader $classLoader)
    {
        return new self(
            new ClassToFileFileToClass(
                new ComposerClassToFile($classLoader),
                new ComposerFileToClass($classLoader)
            )
        );
    }

    public static function fromComposerAutoloaders(array $classLoaders)
    {
        $classToFiles = $fileToClasses = [];
        foreach ($classLoaders as $classLoader) {
            $classToFiles[] = new ComposerClassToFile($classLoader);
        }
        foreach ($classLoaders as $classLoader) {
            $fileToClasses[] = new ComposerFileToClass($classLoader);
        }

        return new self(
            new ClassToFileFileToClass(
                new ChainClassToFile($classToFiles),
                new ChainFileToClass($fileToClasses)
            )
        );
    }

    /**
     * Convert a fully-qualified class name to a list of file paths
     * which may contain it, with the most likely file path being
     * the first in the list.
     */
    public function classToFileCandidates(string $classFullName): FilePathCandidates
    {
        return $this->converter->classToFileCandidates(ClassName::fromString($classFullName));
    }

    /**
     * Convert an absolute file path to a list of class names which it could
     * represent.
     */
    public function fileToClassCandidates(string $filePath): ClassNameCandidates
    {
        return $this->converter->fileToClassCandidates(FilePath::fromString($filePath));
    }
}
