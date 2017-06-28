<?php

namespace DTL\ClassFileConverter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use DTL\ClassFileConverter\Domain\ClassToFile;
use DTL\ClassFileConverter\Domain\ChainClassToFile;
use DTL\ClassFileConverter\Domain\ClassName;
use DTL\ClassFileConverter\Domain\FilePathCandidates;
use DTL\ClassFileConverter\Domain\FilePath;

class ChainClassToFileTest extends TestCase
{
    private $classToFile1;
    private $classToFile2;

    public function setUp()
    {
        $this->classToFile1 = $this->prophesize(ClassToFile::class);
        $this->classToFile2 = $this->prophesize(ClassToFile::class);
    }

    public function testChainClassToFIle()
    {
        $className = ClassName::fromString('Foobar');
        $path1 = FilePath::fromString('one');
        $path2 = FilePath::fromString('two');
        $path3 = FilePath::fromString('three');
        $chain = $this->create([
            $this->classToFile1->reveal(),
            $this->classToFile2->reveal(),
        ]);

        $this->classToFile1->classToFileCandidates($className)->willReturn(
            FilePathCandidates::fromFilePaths([$path1, $path2])
        );
        $this->classToFile2->classToFileCandidates($className)->willReturn(
            FilePathCandidates::fromFilePaths([$path3])
        );

        $candidates = $chain->classToFileCandidates($className);
        $this->assertInstanceOf(FilePathCandidates::class, $candidates);
        $this->assertCount(3, $candidates);
    }

    private function create(array $converters)
    {
        return new ChainClassToFile($converters);
    }
}
