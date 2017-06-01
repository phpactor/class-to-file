<?php

namespace DTL\ClassFileConverter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use DTL\ClassFileConverter\FilePath;
use DTL\ClassFileConverter\FilePathCandidates;

class FilePathCandidatesTest extends TestCase
{
    /**
     * @testdox It can be created from class name instances.
     */
    public function testCreateFromFilePaths()
    {
        $filePaths = [
            FilePath::fromString('Foobar')
        ];

        $candidates = FilePathCandidates::fromFilePaths($filePaths);
        $this->assertEquals($filePaths, iterator_to_array($candidates));
    }
}
