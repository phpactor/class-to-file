<?php

namespace Phpactor\ClassFileConverter\Adapter\Simple;

use Phpactor\ClassFileConverter\Domain\ClassToFile;
use Phpactor\ClassFileConverter\Domain\FilePathCandidates;
use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\ClassFileConverter\Domain\FilePath;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class SimpleClassToFile implements ClassToFile
{
    /**
     * @var string
     */
    private $cwd;

    /**
     * @var ClassScanner
     */
    private $classScanner;

    public function __construct(string $cwd)
    {
        $this->cwd = $cwd;
        $this->classScanner = new ClassScanner();
    }

    public function classToFileCandidates(ClassName $className): FilePathCandidates
    {
        $candidates = [];
        $pattern = sprintf(
            '{^.*/%s.php$}',
            $className->name()
        );

        $iterator = new RecursiveDirectoryIterator($this->cwd);
        $iterator = new RecursiveIteratorIterator($iterator);
        $iterator = new RegexIterator($iterator, $pattern);

        foreach ($iterator as $phpFile) {
            if (ClassName::fromString(
                $this->classScanner->getClassNameFromFile($phpFile->getPathName())
            ) == $className) {
                $candidates[] = FilePath::fromString($phpFile->getPathName());
            }
        }

        return FilePathCandidates::fromFilePaths($candidates);
    }
}
