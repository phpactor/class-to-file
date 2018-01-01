<?php

namespace Phpactor\ClassFileConverter\Domain;

class ChainFileToClass implements FileToClass
{
    /**
     * @var array
     */
    private $converters = [];

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
