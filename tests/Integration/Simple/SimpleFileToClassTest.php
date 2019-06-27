<?php

namespace Phpactor\ClassFileConverter\Tests\Integration\Simple;

use Phpactor\ClassFileConverter\Adapter\Simple\SimpleFileToClass;
use Phpactor\ClassFileConverter\Domain\FilePath;
use Phpactor\ClassFileConverter\Domain\ClassNameCandidates;
use Phpactor\ClassFileConverter\Domain\ClassName;

class SimpleFileToClassTest extends SimpleTestCase
{
    /**
     * @var SimpleFileToClass
     */
    private $fileToClass;

    public function setUp()
    {
        $this->initWorkspace();
        $this->copyProject();
        $this->fileToClass = new SimpleFileToClass();
    }

    public function testFileToClass()
    {
        $candidates = $this->fileToClass->fileToClassCandidates(FilePath::fromString(__DIR__ . '/project/lib/Foobar.php'));

        $this->assertEquals(ClassNameCandidates::fromClassNames([
            ClassName::fromString('Acme\\Foobar')
        ]), $candidates);
    }

    public function testFileToInterface()
    {
        $candidates = $this->fileToClass->fileToClassCandidates(FilePath::fromString(__DIR__ . '/project/lib/FoobarInterface.php'));

        $this->assertEquals(ClassNameCandidates::fromClassNames([
            ClassName::fromString('Acme\\FoobarInterface')
        ]), $candidates);
    }

    public function testFileToTrait()
    {
        $candidates = $this->fileToClass->fileToClassCandidates(FilePath::fromString(__DIR__ . '/project/lib/FoobarTrait.php'));

        $this->assertEquals(ClassNameCandidates::fromClassNames([
            ClassName::fromString('Acme\\FoobarTrait')
        ]), $candidates);
    }

    public function testFileToNoCandidates()
    {
        $candidates = $this->fileToClass->fileToClassCandidates(FilePath::fromString(__DIR__ . '/project/lib/NoClasses.php'));

        $this->assertEquals(ClassNameCandidates::fromClassNames([]), $candidates);
    }

    public function testFileToClassNotExisting()
    {
        $candidates = $this->fileToClass->fileToClassCandidates(FilePath::fromString(__DIR__ . '/project/notexist/NotExist.php'));

        $this->assertEquals(ClassNameCandidates::fromClassNames([]), $candidates);
    }
}
