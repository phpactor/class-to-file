<?php

namespace DTL\ClassFileConverter\Tests\Integration\Composer;

use DTL\ClassFileConverter\Domain\FilePath;
use DTL\ClassFileConverter\Domain\ClassName;
use DTL\ClassFileConverter\Domain\FilePathCandidates;
use DTL\ClassFileConverter\Adapter\Composer\ComposerClassToFile;

/**
 * @runTestsInSeparateProcesses
 */
class ComposerClassToFileTest extends ComposerTestCase
{
    public function setUp()
    {
        $this->initWorkspace();
    }

    /**
     * @testdox PSR-4 class name to a file path.
     */
    public function testPsr4()
    {
        $this->loadExample('psr4-simple.json');
        $this->assertClassNameToFilePath('Acme\\Test\\Foo\\Class', ['psr4/Foo/Class.php']);
    }

    /**
     * @testdox PSR-4 multiple matching prefixes
     */
    public function testPsr4MultipleMatches()
    {
        $this->loadExample('psr4-multiple-matching-prefixes.json');
        $this->assertClassNameToFilePath('Acme\\Test\\Foo\\Bar\\Class', ['psr4/tests/Class.php']);
    }

    /**
     * @testdox PSR-4 with multiple directories.
     */
    public function testPsr4MultipleDirectories()
    {
        $this->loadExample('psr4-multiple.json');
        $this->assertClassNameToFilePath('Acme\\Test\\Class', ['psr4/Class.php', 'psr4-1/Class.php']);
    }

    /**
     * @testdox PSR-0 class name to a file path.
     */
    public function testPsr0()
    {
        $this->loadExample('psr0-simple.json');
        $this->assertClassNameToFilePath('Acme\\Test\\Foo\\Class', ['psr0/Acme/Test/Foo/Class.php']);
    }

    /**
     * @testdox PSR-4 + PSR-0 matching prefixes.
     */
    public function testPsr0AndPsr4()
    {
        $this->loadExample('psr0-psr4.json');
        $this->assertClassNameToFilePath('Acme\\Test\\Class', ['psr4/Class.php', 'psr0/Acme/Test/Class.php']);
    }

    private function assertClassNameToFilePath($className, array $filePaths)
    {
        $filePaths = array_map(function ($filePath) {
            return FilePath::fromParts([$this->workspacePath(), $filePath]);
        }, $filePaths);

        $converter = new ComposerClassToFile($this->getClassLoader());
        $this->assertEquals(
            FilePathCandidates::fromFilePaths($filePaths),
            $converter->classToFileCandidates(ClassName::fromString($className))
        );
    }
}
