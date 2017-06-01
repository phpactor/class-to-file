<?php

namespace DTL\ClassFileConverter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use DTL\ClassFileConverter\ClassName;
use DTL\ClassFileConverter\ClassNameCandidates;

class ClassNameCandidatesTest extends TestCase
{
    /**
     * @testdox It can be created from class name instances.
     */
    public function testCreateFromClassNames()
    {
        $classNames = [
            ClassName::fromString('Foobar')
        ];

        $candidates = ClassNameCandidates::fromClassNames($classNames);
        $this->assertEquals($classNames, iterator_to_array($candidates));


    }
}
