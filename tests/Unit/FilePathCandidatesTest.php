<?php

namespace Phpactor\ClassFileConverter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassFileConverter\Domain\FilePath;
use Phpactor\ClassFileConverter\Domain\FilePathCandidates;

class FilePathCandidatesTest extends TestCase
{
    /**
     * @testdox It can be created from class name instances.
     */
    public function testCreateFromFilePaths()
    {
        $filePaths = [
            FilePath::fromString('Foobar'),
        ];

        $candidates = FilePathCandidates::fromFilePaths($filePaths);
        $this->assertEquals($filePaths, iterator_to_array($candidates));
    }

    /**
     * @testdox it returns the best.
     */
    public function testBest()
    {
        $filePaths = [
            $filePath = FilePath::fromString('Foobar'),
            FilePath::fromString('123'),
            FilePath::fromString('456'),
        ];

        $candidates = FilePathCandidates::fromFilePaths($filePaths);
        $this->assertSame($filePath, $candidates->best());
    }

    /**
     * @testdocs It returns if it is empty or not.
     */
    public function testEmpty()
    {
        $filePaths = [
            FilePath::fromString('Foobar'),
        ];

        $candidates = FilePathCandidates::fromFilePaths($filePaths);
        $this->assertFalse($candidates->noneFound());
        $candidates = FilePathCandidates::fromFilePaths([]);
        $this->assertTrue($candidates->noneFound());
    }

    /**
     * @testdocs It can to array.
     */
    public function testToArray()
    {
        $filePaths = [
            FilePath::fromString('Foobar'),
        ];

        $candidates = FilePathCandidates::fromFilePaths($filePaths);
        $this->assertEquals($candidates->toArray(), $filePaths);
    }
}
