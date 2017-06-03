<?php

namespace DTL\ClassFileConverter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use DTL\ClassFileConverter\ClassToFile;
use DTL\ClassFileConverter\CompositeTransformer;
use DTL\ClassFileConverter\ClassName;
use DTL\ClassFileConverter\FileToClass;
use DTL\ClassFileConverter\FilePathCandidates;
use DTL\ClassFileConverter\ClassNameCandidates;
use DTL\ClassFileConverter\FilePath;

class CompositeTransformerTest extends TestCase
{
    private $transformer;

    public function setUp()
    {
        $this->classToFile = $this->prophesize(ClassToFile::class);
        $this->fileToClass = $this->prophesize(FileToClass::class);

        $this->transformer = new CompositeTransformer(
            $this->classToFile->reveal(),
            $this->fileToClass->reveal()
        );
    }

    /**
     * @testdox It transforms from class to file.
     */
    public function testClassToFile()
    {
        $className = ClassName::fromString('Foo');
        $expectedCandidates = FilePathCandidates::fromFilePaths([]);
        $this->classToFile->classToFileCandidates($className)->willReturn($expectedCandidates);

        $candidates = $this->transformer->classToFileCandidates($className);
        $this->assertSame($expectedCandidates, $candidates);
    }

    /**
     * @testdox It transforms from file to class.
     */
    public function testFileToClass()
    {
        $filePath = FilePath::fromString('Foo');
        $expectedCandidates = ClassNameCandidates::fromClassNames([]);
        $this->fileToClass->fileToClass($filePath)->willReturn($expectedCandidates);

        $candidates = $this->transformer->fileToClass($filePath);
        $this->assertSame($expectedCandidates, $candidates);
    }
}
