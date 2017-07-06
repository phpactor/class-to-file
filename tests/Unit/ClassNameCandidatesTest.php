<?php

namespace Phpactor\ClassFileConverter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\ClassFileConverter\Domain\ClassNameCandidates;

class ClassNameCandidatesTest extends TestCase
{
    /**
     * @testdox It can be created from class name instances.
     */
    public function testCreateFromClassNames()
    {
        $classNames = [
            ClassName::fromString('Foobar'),
        ];

        $candidates = ClassNameCandidates::fromClassNames($classNames);
        $this->assertEquals($classNames, iterator_to_array($candidates));
    }

    /**
     * @testdocs It returns the first match
     */
    public function testReturnsTheBestMatch()
    {
        $classNames = [
            $className = ClassName::fromString('Foobar'),
            ClassName::fromString('Foobar'),
            ClassName::fromString('Foobar'),
        ];

        $candidates = ClassNameCandidates::fromClassNames($classNames);
        $this->assertEquals($className, $candidates->best());
    }

    /**
     * @testdocs It returns if it is empty or not.
     */
    public function testEmpty()
    {
        $classNames = [
            ClassName::fromString('Foobar'),
        ];

        $candidates = ClassNameCandidates::fromClassNames($classNames);
        $this->assertFalse($candidates->noneFound());
        $candidates = ClassNameCandidates::fromClassNames([]);
        $this->assertTrue($candidates->noneFound());
    }
}
