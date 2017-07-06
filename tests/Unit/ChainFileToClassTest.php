<?php

namespace Phpactor\ClassFileConverter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassFileConverter\Domain\FileToClass;
use Phpactor\ClassFileConverter\Domain\ChainFileToClass;
use Phpactor\ClassFileConverter\Domain\ClassNameCandidates;
use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\ClassFileConverter\Domain\FilePath;

class ChainFileToClassTest extends TestCase
{
    private $fileToClass1;
    private $fileToClass2;

    public function setUp()
    {
        $this->fileToClass1 = $this->prophesize(FileToClass::class);
        $this->fileToClass2 = $this->prophesize(FileToClass::class);
    }

    public function testChainClassToFIle()
    {
        $path = FilePath::fromString('Foobar');
        $class1 = ClassName::fromString('one');
        $class2 = ClassName::fromString('two');
        $class3 = ClassName::fromString('three');
        $chain = $this->create([
            $this->fileToClass1->reveal(),
            $this->fileToClass2->reveal(),
        ]);

        $this->fileToClass1->fileToClassCandidates($path)->willReturn(
            ClassNameCandidates::fromClassNames([$class1, $class2])
        );
        $this->fileToClass2->fileToClassCandidates($path)->willReturn(
            ClassNameCandidates::fromClassNames([$class3])
        );

        $candidates = $chain->fileToClassCandidates($path);
        $this->assertInstanceOf(ClassNameCandidates::class, $candidates);
        $this->assertCount(3, $candidates);
    }

    private function create(array $converters)
    {
        return new ChainFileToClass($converters);
    }
}
