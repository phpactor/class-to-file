<?php

namespace DTL\ClassFileConverter\Tests\Integration\Composer;

use DTL\ClassFileConverter\Composer\ComposerClassFileConverter;
use DTL\ClassFileConverter\FilePath;
use DTL\ClassFileConverter\ClassName;
use DTL\ClassFileConverter\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;
use DTL\ClassFileConverter\FilePathCandidates;
use DTL\ClassFileConverter\Composer\ComposerFileToClass;
use DTL\ClassFileConverter\ClassNameCandidates;

/**
 * @runTestsInSeparateProcesses
 */
class ComposerFileToClassTest extends ComposerTestCase
{
    public function setUp()
    {
        $this->initWorkspace();
    }

    /**
     * @testdox PSR-4 file to class.
     */
    public function testPsr4()
    {
        $this->loadExample('psr4-simple.json');
        $this->assertFilePathToClassName('/psr4/Foo/Class.php', [ 'Acme\\Test\\Foo\\Class' ]);
    }

    /**
     * @testdox PSR-4 multiple matching prefixes
     */
    public function testPsr4MultipleMatches()
    {
        $this->loadExample('psr4-multiple-matching-prefixes.json');
        $this->assertFilePathToClassName('/psr4/tests/Class.php', [
            'Acme\\Test\\Foo\\Bar\\Class',
            'Acme\\Test\\tests\\Class'
        ]);
    }

    /**
     * @testdox PSR-4 with multiple directories.
     */
    public function testPsr4MultipleDirectories()
    {
        $this->loadExample('psr4-multiple.json');
        $this->assertFilePathToClassName(
            '/psr4/Class.php',
            [
                'Acme\\Test\\Class'
            ]
        );
    }

    /**
     * @testdox PSR-0 class name to a file path.
     */
    public function testPsr0()
    {
        $this->loadExample('psr0-simple.json');
        $this->assertFilePathToClassName(
            '/psr0/Acme/Test/Foo/Class.php',
            [ 'Acme\\Test\\Foo\\Class' ]
        );
    }

    /**
     * @testdox PSR-4 + PSR-0 with conflict
     */
    public function testPsr0AndPsr4Conflict()
    {
        $this->loadExample('psr0-psr4-conflict.json');
        $this->assertFilePathToClassName('/src/Acme/Test/Class.php', [
            'Acme\\Test\\Acme\\Test\\Class', // psr4 first
            'Acme\\Test\\Class' // psr0 second
        ]);
    }

    private function assertFilePathToClassName($filePath, array $classNames)
    {
        $converter = new ComposerFileToClass($this->getClassLoader());
        $expected = ClassNameCandidates::fromClassNames(array_map(function ($className) {
            return ClassName::fromString($className);
        }, $classNames));

        $actual = $converter->fileToClass(FilePath::fromString($this->workspacePath() . $filePath));

        $this->assertEquals($expected, $actual);
    }
}
