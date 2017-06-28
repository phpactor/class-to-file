<?php

namespace DTL\ClassFileConverter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use DTL\ClassFileConverter\Domain\ClassToFile;
use DTL\ClassFileConverter\Domain\ClassToFileFileToClass;
use DTL\ClassFileConverter\Domain\ClassName;
use DTL\ClassFileConverter\Domain\FileToClass;
use DTL\ClassFileConverter\Domain\FilePathCandidates;
use DTL\ClassFileConverter\Domain\ClassNameCandidates;
use DTL\ClassFileConverter\Domain\FilePath;

class CompositeTransformerTest extends TestCase
{
    private $transformer;

    public function setUp()
    {
        $this->classToFile = $this->prophesize(ClassToFile::class);
        $this->fileToClassCandidates = $this->prophesize(FileToClass::class);

        $this->transformer = new ClassToFileFileToClass(
            $this->classToFile->reveal(),
            $this->fileToClassCandidates->reveal()
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
        $this->fileToClassCandidates->fileToClassCandidates($filePath)->willReturn($expectedCandidates);

        $candidates = $this->transformer->fileToClassCandidates($filePath);
        $this->assertSame($expectedCandidates, $candidates);
    }
}
