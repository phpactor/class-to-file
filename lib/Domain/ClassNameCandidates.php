<?php

namespace Phpactor\ClassFileConverter\Domain;

use IteratorAggregate;
use ArrayIterator;
use RuntimeException;
use Traversable;

final class ClassNameCandidates implements IteratorAggregate
{
    private $classNames = [];

    private function __construct(array $classNames)
    {
        foreach ($classNames as $className) {
            $this->add($className);
        }
    }

    public static function fromClassNames(array $classNames)
    {
        return new self($classNames);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator(array_values($this->classNames));
    }

    public function noneFound(): bool
    {
        return empty($this->classNames);
    }

    public function best(): ClassName
    {
        if (empty($this->classNames)) {
            throw new RuntimeException(
                'There are no class name candidates'
            );
        }

        return reset($this->classNames);
    }

    private function add(ClassName $className): void
    {
        $this->classNames[$className->__toString()] = $className;
    }
}
