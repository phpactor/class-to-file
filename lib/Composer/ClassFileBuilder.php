<?php

namespace DTL\ClassFileConverter\Composer;

use Composer\Autoload\ClassLoader;
use DTL\ClassFileConverter\FileToClass;
use DTL\ClassFileConverter\ClassToFile;
use DTL\ClassFileConverter\FilePathCandidates;
use DTL\ClassFileConverter\ClassName;
use DTL\ClassFileConverter\FilePath;

final class ComposerClassFileTransformer implements ClassToFile, FileToClass
{
    private $classToFile;
    private $fileToClass;

    public function __construct(ClassLoader $loader, ClassToFile $classToFile, FileToClass $fileToClass)
    {
        $this->classToFile = new ClassToFile($loader);
        $this->fileToClass = new FileToClass($loader);
    }

    public static function create(ClassLoader $loader)
    {
        return new self($loader);
    }

    public function fileToClass(FilePath $filePath): ClassNameCandidates
    {
        return $this->fileToClass->fileToClass($filePath);
    }

    public function classToFileCandidates(ClassName $className): FilePathCandidates
    {
        return $this->classToFile->classToFileCandidates($classname);
    }
}
