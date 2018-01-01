<?php

namespace Phpactor\ClassFileConverter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassFileConverter\Domain\ClassName;

class ClassNameTest extends TestCase
{
    /**
     * @dataProvider provideBeginsWith
     */
    public function testBeginsWith($name, $prefix, $expected)
    {
        $className = ClassName::fromString($name);
        $this->assertEquals($expected, $className->beginsWith($prefix));
    }

    public function provideBeginsWith()
    {
        return [
            [
                'Foobar\\BarFoo',
                'Foobar',
                true,
            ],
            [
                'Foobar\\BarFoo',
                'Foobar\\BarFoo',
                true,
            ],
            [
                'Foobar\\BarFoo',
                'Foobar\\BarFoo\\BarBar',
                false,
            ],
            [
                'Foobar\\BarFoo',
                'BarBar\\BarFoo\\BarBar',
                false,
            ],
        ];
    }

    public function testName()
    {
        $class = ClassName::fromString('Foobar\\Barfoo');
        $this->assertEquals('Barfoo', $class->name());
    }

    public function testNameShort()
    {
        $class = ClassName::fromString('Barfoo');
        $this->assertEquals('Barfoo', $class->name());
    }
}
