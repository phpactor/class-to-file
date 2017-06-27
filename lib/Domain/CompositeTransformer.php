<?php

namespace DTL\ClassFileConverter\Domain;

use DTL\ClassFileConverter\Domain\ClassName;
use DTL\ClassFileConverter\Domain\ClassNameCandidates;
use DTL\ClassFileConverter\Domain\ClassToFile;

final class CompositeTransformer implements ClassToFile, FileToClass
{
    private $classToFile;
    private $fileToClass;

    public function __construct(ClassToFile $classToFile, FileToClass $fileToClass)
    {
        $this->classToFile = $classToFile;
        $this->fileToClass = $fileToClass;
    }

    public function fileToClass(FilePath $filePath): ClassNameCandidates
    {
        return $this->fileToClass->fileToClass($filePath);
    }

    public function classToFileCandidates(ClassName $className): FilePathCandidates
    {
        return $this->classToFile->classToFileCandidates($className);
    }
}
