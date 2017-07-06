<?php

namespace Phpactor\ClassFileConverter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassFileConverter\Domain\ClassToFile;
use Phpactor\ClassFileConverter\Domain\ClassToFileFileToClass;
use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\ClassFileConverter\Domain\FileToClass;
use Phpactor\ClassFileConverter\Domain\FilePathCandidates;
use Phpactor\ClassFileConverter\Domain\ClassNameCandidates;
use Phpactor\ClassFileConverter\Domain\FilePath;

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
