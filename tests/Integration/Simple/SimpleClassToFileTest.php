<?php

namespace Phpactor\ClassFileConverter\Tests\Integration\Simple;

use Phpactor\ClassFileConverter\Adapter\Simple\SimpleClassToFile;
use Phpactor\ClassFileConverter\Domain\FilePath;
use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\ClassFileConverter\Domain\FilePathCandidates;

class SimpleClassToFileTest extends SimpleTestCase
{
    /**
     * @var SimpleClassToFile
     */
    private $classToFile;

    public function setUp()
    {
        $this->initWorkspace();
        $this->copyProject();
        $this->classToFile = new SimpleClassToFile($this->workspacePath());
    }

    public function testClassToFile()
    {
        $candidates = $this->classToFile->classToFileCandidates(ClassName::fromString('Acme\\Foobar'));

        $this->assertEquals(FilePathCandidates::fromFilePaths([
            FilePath::fromString(__DIR__ . '/../../Workspace/lib/Foobar.php')
        ]), $candidates);
    }

    public function testClassToFileDeeper()
    {
        $candidates = $this->classToFile->classToFileCandidates(ClassName::fromString('Acme\\NamespaceHere\\Hallo'));

        $this->assertEquals(FilePathCandidates::fromFilePaths([
            FilePath::fromString(__DIR__ . '/../../Workspace/lib/NamespaceHere/Hallo.php')
        ]), $candidates);
    }

    public function testClassToNoCandidates()
    {
        $candidates = $this->classToFile->classToFileCandidates(ClassName::fromString('Zog\\Foobar'));
        $this->assertEquals(FilePathCandidates::fromFilePaths([]), $candidates);
    }
}
