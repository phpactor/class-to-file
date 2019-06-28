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
        $this->assertFilePathToClassName('/psr4/Foo/Class.php', ['Acme\\Test\\Foo\\Class']);
    }

    /**
     * @testdox PSR-4 file with relative path components
     */
    public function testPsr4WithRelativePathComponents()
    {
        $this->loadExample('psr4-simple.json');
        $this->assertFilePathToClassName('/psr4/Foo/../Foo/Class.php', ['Acme\\Test\\Foo\\Class']);
    }

    /**
     * @testdox PSR-4 multiple matching prefixes
     */
    public function testPsr4MultipleMatches()
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
    public function testPsr4MultipleDirectories()
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
    public function testPsr0()
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
    public function testPsr0AndPsr4Conflict()
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
    public function testPsr0Fallback()
    {
        $this->loadExample('psr0-fallback.json');
        $this->assertFilePathToClassName('/psr0/Acme/Test/Class.php', [
            'Acme\\Test\\Class',
        ]);
    }

    /**
     * @testdox PSR-4 no fallback
     */
    public function testPsr4Fallback()
    {
        $this->loadExample('psr4-fallback.json');
        $this->assertFilePathToClassName('/psr4/Acme/Test/Class.php', [
            'Acme\\Test\\Class',
        ]);
    }

    public function testNestedPsr4Directories()
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
    public function testClassmap()
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
    public function testNonExistingDirectory()
    {
        $this->loadExample('nonexisting.json');
        $this->assertFilePathToClassName(
            '/nonexisting/Post.php',
            [ 'Acme\\Test\\Post' ]
        );
    }


    private function assertFilePathToClassName($filePath, array $classNames)
    {
        $converter = new ComposerFileToClass($this->getClassLoader());
        $expected = ClassNameCandidates::fromClassNames(array_map(function ($className) {
            return ClassName::fromString($className);
        }, $classNames));

        $actual = $converter->fileToClassCandidates(FilePath::fromString($this->workspacePath().$filePath));

        $this->assertEquals($expected, $actual);
    }
}
