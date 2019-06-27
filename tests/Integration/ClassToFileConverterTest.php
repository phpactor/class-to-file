<?php

namespace Phpactor\ClassFileConverter\Tests\Integration;

use Phpactor\ClassFileConverter\ClassToFileConverter;
use Phpactor\ClassFileConverter\Domain\FilePath;
use Phpactor\ClassFileConverter\Domain\ClassName;

/**
 * @runTestsInSeparateProcesses
 */
class ClassToFileConverterTest extends IntegrationTestCase
{
    private $classLoader;

    public function setUp()
    {
        $this->classLoader = require __DIR__.'/../../vendor/autoload.php';
    }

    /**
     * It can build itself using a composer autoloader.
     */
    public function testCreateForComposer()
    {
        $converter = ClassToFileConverter::fromComposerAutoloader($this->classLoader);
        $candidates = $converter->fileToClassCandidates(FilePath::fromString(__FILE__));
        $this->assertEquals(
            __CLASS__,
            (string) $candidates->best()
        );

        $candidates = $converter->classToFileCandidates(ClassName::fromString(__CLASS__));
        $this->assertEquals(
            __FILE__,
            (string) $candidates->best()
        );
    }

    /**
     * It can build itself using a series of composer autoloaders.
     */
    public function testCreateForComposers()
    {
        $converter = ClassToFileConverter::fromComposerAutoloaders([$this->classLoader]);
        $candidates = $converter->fileToClassCandidates(FilePath::fromString(__FILE__));
        $this->assertEquals(
            __CLASS__,
            (string) $candidates->best()
        );

        $candidates = $converter->classToFileCandidates(ClassName::fromString(__CLASS__));
        $this->assertEquals(
            __FILE__,
            (string) $candidates->best()
        );
    }
}
