<?php

namespace Phpactor\ClassFileConverter\Tests\Integration\Simple;

use Phpactor\ClassFileConverter\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;
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

    public function testFileToNoCandidates()
    {
        $candidates = $this->fileToClass->fileToClassCandidates(FilePath::fromString(__DIR__ . '/project/lib/NoClasses.php'));

        $this->assertEquals(ClassNameCandidates::fromClassNames([]), $candidates);
    }
}
