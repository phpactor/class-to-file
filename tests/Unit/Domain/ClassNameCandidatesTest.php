<?php

namespace Phpactor\ClassFileConverter\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\ClassFileConverter\Domain\ClassNameCandidates;

class ClassNameCandidatesTest extends TestCase
{
    public function testEnsuresClassNamesAreUnique(): void
    {
        $candidates = ClassNameCandidates::fromClassNames([
            ClassName::fromString('Foobar'),
            ClassName::fromString('Foobar'),
            ClassName::fromString('Barfoo'),
        ]);

        self::assertCount(2, $candidates);
    }
}
