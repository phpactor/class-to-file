<?php

namespace DTL\ClassFileConverter\Domain;

use DTL\ClassFileConverter\Domain\ClassName;
use DTL\ClassFileConverter\Domain\ClassToFile;

class ChainClassToFile implements ClassToFile
{
    public function __construct(array $converters)
    {
        foreach ($converters as $converter) {
            $this->add($converter);
        }
    }

    public function classToFileCandidates(ClassName $className): FilePathCandidates
    {
        $paths = [];
        foreach ($this->converters as $converter) {
            foreach ($converter->classToFileCandidates($className) as $candidate) {
                $paths[] = $candidate;
            }
        }

        return FilePathCandidates::fromFilePaths($paths);
    }

    private function add(ClassToFile $classToFile)
    {
        $this->converters[] = $classToFile;
    }
}
