<?php

namespace Phpactor\ClassFileConverter\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassFileConverter\Domain\FilePath;

class FilePathTest extends TestCase
{
    /**
     * @dataProvider provideIsAbsolute
     */
    public function testIsAbsolute(string $path, bool $expected)
    {
        $path = FilePath::fromString($path);
        self::assertEquals($expected, $path->isAbsolute());
    }

    public function provideIsAbsolute()
    {
        yield 'not absolute unix' => [
            'foobar/barfoo',
            false,
        ];

        yield 'absolute unix' => [
            '/foobar/barfoo',
            true,
        ];

        yield 'absolute windows' => [
            'c:\foobar\barfoo',
            true,
        ];

        yield 'not absolute windows' => [
            'foobar\barfoo',
            false,
        ];

        yield 'absolute phar' => [
            'phar:///barfoo',
            true
        ];

        yield 'not absolute phar' => [
            'phar://barfoo',
            false,
        ];
    }
}
