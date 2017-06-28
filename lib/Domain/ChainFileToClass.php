<?php

namespace DTL\ClassFileConverter\Domain;

use DTL\ClassFileConverter\Domain\ClassName;
use DTL\ClassFileConverter\Domain\ClassNameCandidates;
use DTL\ClassFileConverter\Domain\ClassToFile;
use DTL\ClassFileConverter\Domain\FilePath;

class ChainFileToClass implements FileToClass
{
    public function __construct(array $converters)
    {
        foreach ($converters as $converter) {
            $this->add($converter);
        }
    }

    public function fileToClassCandidates(FilePath $filePath): ClassNameCandidates
    {
        $classNames = [];
        foreach ($this->converters as $converter) {
            foreach ($converter->fileToClassCandidates($filePath) as $candidate) {
                $classNames[] = $candidate;
            }
        }

        return ClassNameCandidates::fromClassNames($classNames);
    }

    private function add(FileToClass $fileToClass)
    {
        $this->converters[] = $fileToClass;
    }
}
