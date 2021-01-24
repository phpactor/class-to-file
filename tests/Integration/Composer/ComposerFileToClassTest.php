<?php

namespace Phpactor\ClassFileConverter\Tests\Integration\Composer;

use Phpactor\ClassFileConverter\Domain\FilePath;
use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\ClassFileConverter\Adapter\Composer\ComposerFileToClass;
use Phpactor\ClassFileConverter\Domain\ClassNameCandidates;

/**
 * @runTestsInSeparateProcesses
 */
class ComposerFileToClassTest extends ComposerTestCase
{
    public function setUp(): void
    {
        $this->initWorkspace();
    }

    /**
     * @testdox PSR-4 file to class.
     */
    public function testPsr4(): void
    {
        $this->loadExample('psr4-simple.json');
        $this->assertFilePathToClassName('/psr4/Foo/Class.php', ['Acme\\Test\\Foo\\Class']);
    }

    /**
     * @testdox PSR-4 file with relative path components
     */
    public function testPsr4WithRelativePathComponents(): void
    {
        $this->loadExample('psr4-simple.json');
        $this->assertFilePathToClassName('/psr4/Foo/../Foo/Class.php', ['Acme\\Test\\Foo\\Class']);
    }

    /**
     * @testdox PSR-4 multiple matching prefixes
     */
    public function testPsr4MultipleMatches(): void
    {
        $this->loadExample('psr4-multiple-matching-prefixes.json');
        $this->assertFilePathToClassName('/psr4/tests/Class.php', [
            'Acme\\Test\\Foo\\Bar\\Class',
            'Acme\\Test\\tests\\Class',
        ]);
    }

    /**
     * @testdox PSR-4 with multiple directories.
     */
    public function testPsr4MultipleDirectories(): void
    {
        $this->loadExample('psr4-multiple.json');
        $this->assertFilePathToClassName(
            '/psr4/Class.php',
            [
                'Acme\\Test\\Class',
            ]
        );
    }

    /**
     * @testdox PSR-0 class name to a file path.
     */
    public function testPsr0(): void
    {
        $this->loadExample('psr0-simple.json');
        $this->assertFilePathToClassName(
            '/psr0/Acme/Test/Foo/Class.php',
            ['Acme\\Test\\Foo\\Class']
        );
    }

    /**
     * @testdox PSR-4 + PSR-0 with conflict
     */
    public function testPsr0AndPsr4Conflict(): void
    {
        $this->loadExample('psr0-psr4-conflict.json');
        $this->assertFilePathToClassName('/src/Acme/Test/Class.php', [
            'Acme\\Test\\Acme\\Test\\Class', // psr4 first
            'Acme\\Test\\Class', // psr0 second
        ]);
    }

    /**
     * @testdox PSR-0 no prefix
     */
    public function testPsr0Fallback(): void
    {
        $this->loadExample('psr0-fallback.json');
        $this->assertFilePathToClassName('/psr0/Acme/Test/Class.php', [
            'Acme\\Test\\Class',
        ]);
    }

    /**
     * @testdox PSR-4 no fallback
     */
    public function testPsr4Fallback(): void
    {
        $this->loadExample('psr4-fallback.json');
        $this->assertFilePathToClassName('/psr4/Acme/Test/Class.php', [
            'Acme\\Test\\Class',
        ]);
    }

    public function testNestedPsr4Directories(): void
    {
        $this->loadExample('psr4-nested.json');
        $this->assertFilePathToClassName('/psr4/AppBundle/Foo.php', [
            'AppBundle\\Foo',
            'App\\AppBundle\\Foo'
        ]);
    }


    /**
     * @testdox Loads from classmap
     */
    public function testClassmap(): void
    {
        $this->loadExample('classmap.json');
        $this->assertFilePathToClassName(
            '/classmap/Acme/Post.php',
            [ 'Random\\Name\\Generator', 'Random\\Name\\SubGenerator'  ]
        );
    }

    /**K
     * @testdox Loads with non-existing directory
     */
    public function testNonExistingDirectory(): void
    {
        $this->loadExample('nonexisting.json');
        $this->assertFilePathToClassName(
            '/nonexisting/Post.php',
            [ 'Acme\\Test\\Post' ]
        );
    }


    private function assertFilePathToClassName($filePath, array $classNames): void
    {
        $converter = new ComposerFileToClass($this->getClassLoader());
        $expected = ClassNameCandidates::fromClassNames(array_map(function ($className) {
            return ClassName::fromString($className);
        }, $classNames));

        $actual = $converter->fileToClassCandidates(FilePath::fromString($this->workspacePath().$filePath));

        $this->assertEquals($expected, $actual);
    }
}
