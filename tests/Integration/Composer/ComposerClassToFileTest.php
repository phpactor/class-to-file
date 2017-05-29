<?php

namespace DTL\ClassFileConverter\Tests\Integration\Composer;

use DTL\ClassFileConverter\Composer\ComposerClassFileConverter;
use DTL\ClassFileConverter\FilePath;
use DTL\ClassFileConverter\ClassName;
use DTL\ClassFileConverter\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;
use DTL\ClassFileConverter\FilePathCandidates;

/**
 * @runTestsInSeparateProcesses
 */
class ComposerClassFileConverterTest extends ComposerTestCase
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
        $this->assertClassNameToFilePath('Acme\\Test\\Foo\\Class', [ 'psr4/Foo/Class.php' ]);
    }

    /**
     * @testdox PSR-4 multiple matching prefixes
     */
    public function testPsr4MultipleMatches()
    {
        $this->loadExample('psr4-multiple-matching-prefixes.json');
        $this->assertClassNameToFilePath('Acme\\Test\\Foo\\Bar\\Class', [ 'psr4/tests/Class.php' ]);
    }

    /**
     * @testdox PSR-4 with multiple directories.
     */
    public function testPsr4MultipleDirectories()
    {
        $this->loadExample('psr4-multiple.json');
        $this->assertClassNameToFilePath('Acme\\Test\\Class', [ 'psr4/Class.php', 'psr4-1/Class.php']);
    }

    /**
     * @testdox PSR-0 class name to a file path.
     */
    public function testPsr0()
    {
        $this->loadExample('psr0-simple.json');
        $this->assertClassNameToFilePath('Acme\\Test\\Foo\\Class', [ 'psr0/Acme/Test/Foo/Class.php' ]);
    }

    /**
     * @testdox PSR-4 + PSR-0 matching prefixes.
     */
    public function testPsr0AndPsr4()
    {
        $this->loadExample('psr0-psr4.json');
        $this->assertClassNameToFilePath('Acme\\Test\\Class', [ 'psr0/Acme/Test/Class.php', 'psr4/Class.php' ]);
    }

    private function assertClassNameToFilePath($className, array $filePaths)
    {
        $filePaths = array_map(function ($filePath) {
            return FilePath::fromParts([$this->workspacePath(), $filePath]);
        }, $filePaths);

        $converter = new ComposerClassFileConverter($this->getClassLoader());
        $this->assertEquals(
            FilePathCandidates::fromFilePaths($filePaths),
            $converter->classToFile(ClassName::fromString($className))
        );
    }
}
