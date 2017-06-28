<?php

namespace DTL\ClassFileConverter\Domain;

use DTL\ClassFileConverter\Domain\ClassName;

final class ClassToFileFileToClass
{
    private $classToFile;
    private $fileToClass;

    public function __construct(ClassToFile $classToFile, FileToClass $fileToClass)
    {
        $this->classToFile = $classToFile;
        $this->fileToClassCandidates = $fileToClass;
    }

    public function fileToClassCandidates(FilePath $filePath): ClassNameCandidates
    {
        return $this->fileToClassCandidates->fileToClassCandidates($filePath);
    }

    public function classToFileCandidates(ClassName $className): FilePathCandidates
    {
        return $this->classToFile->classToFileCandidates($className);
    }
}
