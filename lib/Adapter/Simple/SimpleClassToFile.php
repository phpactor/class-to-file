<?php

namespace Phpactor\ClassFileConverter\Adapter\Simple;

use Phpactor\ClassFileConverter\Domain\ClassToFile;
use Phpactor\ClassFileConverter\Domain\FilePathCandidates;
use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\ClassFileConverter\Domain\FilePath;

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
        foreach (glob(sprintf(
            '%s/**/%s.php',
            $this->cwd,
            $className->name()
        )) as $phpFile) {
            if (ClassName::fromString(
                $this->classScanner->getClassNameFromFile($phpFile)
            ) == $className) {
                $candidates[] = FilePath::fromString($phpFile);
            }
        }

        return FilePathCandidates::fromFilePaths($candidates);
    }
}
