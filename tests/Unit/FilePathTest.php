<?php

namespace DTL\ClassFileConverter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use DTL\ClassFileConverter\Domain\FilePath;

class FilePathTest extends TestCase
{
    public function testIsAbsolute()
    {
        $this->assertTrue(FilePath::fromString('/path/to/foo')->isAbsolute());
        $this->assertFalse(FilePath::fromString('path/to/foo')->isAbsolute());
    }
}
