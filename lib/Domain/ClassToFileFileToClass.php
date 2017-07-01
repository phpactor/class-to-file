<?php

namespace DTL\ClassFileConverter\Domain;

final class ClassToFileFileToClass implements ClassToFile, FileToClass
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
