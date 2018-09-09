<?php

namespace Phpactor\ClassFileConverter\Tests\Integration\Composer;

use Phpactor\ClassFileConverter\Domain\FilePath;
use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\ClassFileConverter\Domain\FilePathCandidates;
use Phpactor\ClassFileConverter\Adapter\Composer\ComposerClassToFile;
use Psr\Log\LoggerInterface;
use Prophecy\Argument;

/**
 * @runTestsInSeparateProcesses
 */
class ComposerClassToFileTest extends ComposerTestCase
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ComposerClassToFile
     */
    private $converter;

    public function setUp()
    {
        $this->initWorkspace();
        $this->logger = $this->prophesize(LoggerInterface::class);
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
     * @testdox PSR-4 class in dev namespace
     */
    public function testPsr4Dev()
    {
        $this->loadExample('psr4-dev.json');
        $this->assertClassNameToFilePath('Acme\\Dev\\Foo\\Class', ['psr4-dev/Foo/Class.php']);
    }

    /**
     * @testdox PSR-4 multiple matching prefixes
     */
    public function testPsr4MultipleMatchesLongestPrefixesFirst()
    {
        $this->loadExample('psr4-multiple-matching-prefixes.json');
        $this->assertClassNameToFilePath('Acme\\Test\\Foo\\Bar\\Class', [
            'psr4/tests/Class.php',
            'psr4/tests/foo/Bar/Class.php',
            'psr4/Foo/Bar/Class.php'
        ]);
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
        $this->assertClassNameToFilePath('Acme\\Test\\Class', [
            'psr4/Class.php',
            'psr0/Acme/Test/Class.php'
        ]);
    }

    /**
     * @testdox PSR-0 fallback
     */
    public function testPsr0Fallback()
    {
        $this->loadExample('psr0-fallback.json');
        $this->assertClassNameToFilePath('Acme\\Test\\Class', [ 'psr0/Acme/Test/Class.php' ]);
    }

    /**
     * @testdox PSR-0 short name prefix
     */
    public function testPsr0ShortNamePrefix()
    {
        $this->loadExample('psr0-short-prefix.json');
        $this->assertClassNameToFilePath('Twig_Extension', [ 'psr0/twig/Twig/Extension.php' ]);
    }

    public function testPsr0ShortNamePrefix2()
    {
        $this->loadExample('psr0-short-prefix.json');
        $this->assertClassNameToFilePath('Twig_Tests_Extension', [ 'psr0/twig/Twig/Tests/Extension.php' ]);
    }


    /**
     * @testdox PSR-4 fallback
     */
    public function testPsr4Fallback()
    {
        $this->loadExample('psr4-fallback.json');
        $this->assertClassNameToFilePath('Acme\\Test\\Class', [ 'psr4/Acme/Test/Class.php' ]);
    }

    /**
     * @testdox Loads from classmap
     */
    public function testClassmap()
    {
        $this->loadExample('classmap.json');
        $this->assertClassNameToFilePath(
            'Random\\Name\\Generator',
            ['classmap/Acme/Post.php']
        );
    }

    /**
     * @testdox Ignores invalid directories.
     */
    public function testIgnoresInvalidDirs()
    {
        $this->loadExample('invalid-dir.json');
        $this->logger->warning(Argument::containingString('Composer mapped path'))->shouldBeCalledTimes(1);

        $this->assertClassNameToFilePath(
            'Acme\\Foo\\Generator',
            ['vendor/composer/../../_invalid__/Foo/Generator.php']
        );
    }

    private function assertClassNameToFilePath($className, array $filePaths, array $messages = [])
    {
        $filePaths = array_map(function ($filePath) {
            return FilePath::fromParts([$this->workspacePath(), $filePath]);
        }, $filePaths);

        $converter = new ComposerClassToFile($this->getClassLoader(), $this->logger->reveal());
        $this->assertEquals(
            FilePathCandidates::fromFilePaths($filePaths),
            $converter->classToFileCandidates(ClassName::fromString($className))
        );
    }
}
