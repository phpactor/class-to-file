<?php

namespace Phpactor\ClassFileConverter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassFileConverter\Domain\FilePath;

class FilePathTest extends TestCase
{
    public function testIsAbsolute()
    {
        $this->assertTrue(FilePath::fromString('/path/to/foo')->isAbsolute());
        $this->assertFalse(FilePath::fromString('path/to/foo')->isAbsolute());
    }
}
