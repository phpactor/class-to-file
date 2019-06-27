<?php

namespace Phpactor\ClassFileConverter\Adapter\Simple;

use Phpactor\ClassFileConverter\Domain\FileToClass;
use Phpactor\ClassFileConverter\Domain\ClassNameCandidates;
use Phpactor\ClassFileConverter\Domain\FilePath;
use Phpactor\ClassFileConverter\Domain\ClassName;

class SimpleFileToClass implements FileToClass
{
    /**
     * @var ClassScanner
     */
    private $classScanner;

    public function __construct()
    {
        $this->classScanner = new ClassScanner();
    }

    public function fileToClassCandidates(FilePath $filePath): ClassNameCandidates
    {
        $classNames = [];

        $className = $this->classScanner->getClassNameFromFile($filePath->__toString());

        if ($className) {
            $classNames[] = ClassName::fromString($className);
        }

        return ClassNameCandidates::fromClassNames($classNames);
    }
}
