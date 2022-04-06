<?php

namespace Phpactor\ClassFileConverter\Tests\Integration\Composer;

use Phpactor\ClassFileConverter\Domain\FilePath;
use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\ClassFileConverter\Domain\FilePathCandidates;
use Phpactor\ClassFileConverter\Adapter\Composer\ComposerClassToFile;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Prophecy\Argument;

/**
 * @runTestsInSeparateProcesses
 */
class ComposerClassToFileTest extends ComposerTestCase
{
    use ProphecyTrait;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ComposerClassToFile
     */
    private $converter;

    public function setUp(): void
    {
        $this->initWorkspace();
        $this->logger = $this->prophesize(LoggerInterface::class);
    }

    /**
     * @testdox PSR-4 class name to a file path.
     */
    public function testPsr4(): void
    {
        $this->loadExample('psr4-simple.json');
        $this->assertClassNameToFilePath('Acme\\Test\\Foo\\Class', ['psr4/Foo/Class.php']);
    }

    /**
     * @testdox PSR-4 class name to a file path.
     */
    public function testPsr4WithClassmapAuthoritative(): void
    {
        $this->loadExample('psr4-classmap-authoritative.json');
        $this->getClassLoader()->addClassMap(['Acme\\Test\\Foo\\Bar' => $this->workspacePath() . '/psr4/Foo/Bar.php']);
        $this->assertClassNameToFilePath('Acme\\Test\\Foo\\Bar2', ['psr4/Foo/Bar2.php']);
        $this->assertClassNameToFilePath('Acme\\Test\\Foo\\Class2', ['psr4/Foo/Class2.php']);
    }

    /**
     * @testdox PSR-4 class in dev namespace
     */
    public function testPsr4Dev(): void
    {
        $this->loadExample('psr4-dev.json');
        $this->assertClassNameToFilePath('Acme\\Dev\\Foo\\Class', ['psr4-dev/Foo/Class.php']);
    }

    /**
     * @testdox PSR-4 multiple matching prefixes
     */
    public function testPsr4MultipleMatchesLongestPrefixesFirst(): void
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
    public function testPsr4MultipleDirectories(): void
    {
        $this->loadExample('psr4-multiple.json');
        $this->assertClassNameToFilePath('Acme\\Test\\Class', ['psr4/Class.php', 'psr4-1/Class.php']);
    }

    /**
     * @testdox PSR-0 class name to a file path.
     */
    public function testPsr0(): void
    {
        $this->loadExample('psr0-simple.json');
        $this->assertClassNameToFilePath('Acme\\Test\\Foo\\Class', ['psr0/Acme/Test/Foo/Class.php']);
    }

    /**
     * @testdox PSR-4 + PSR-0 matching prefixes.
     */
    public function testPsr0AndPsr4(): void
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
    public function testPsr0Fallback(): void
    {
        $this->loadExample('psr0-fallback.json');
        $this->assertClassNameToFilePath('Acme\\Test\\Class', [ 'psr0/Acme/Test/Class.php' ]);
    }

    /**
     * @testdox PSR-0 short name prefix
     */
    public function testPsr0ShortNamePrefix(): void
    {
        $this->loadExample('psr0-short-prefix.json');
        $this->assertClassNameToFilePath('Twig_Extension', [ 'psr0/twig/Twig/Extension.php' ]);
    }

    public function testPsr0ShortNamePrefix2(): void
    {
        $this->loadExample('psr0-short-prefix.json');
        $this->assertClassNameToFilePath('Twig_Tests_Extension', [ 'psr0/twig/Twig/Tests/Extension.php' ]);
    }

    /**
     * @testdox PSR-4 fallback
     */
    public function testPsr4Fallback(): void
    {
        $this->loadExample('psr4-fallback.json');
        $this->assertClassNameToFilePath('Acme\\Test\\Class', [ 'psr4/Acme/Test/Class.php' ]);
    }

    /**
     * @testdox Loads from classmap
     */
    public function testClassmap(): void
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
    public function testIgnoresInvalidDirs(): void
    {
        $this->loadExample('invalid-dir.json');
        $this->logger->warning(Argument::containingString('Composer mapped path'))->shouldBeCalledTimes(1);

        $this->assertClassNameToFilePath(
            'Acme\\Foo\\Generator',
            ['vendor/composer/../../_invalid__/Foo/Generator.php']
        );
    }

    private function assertClassNameToFilePath($className, array $filePaths, array $messages = []): void
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
