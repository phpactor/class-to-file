<?php

namespace Phpactor\ClassFileConverter\Domain;

final class ClassNameCandidates implements \IteratorAggregate
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

    public function getIterator()
    {
        return new \ArrayIterator($this->classNames);
    }

    public function noneFound(): bool
    {
        return empty($this->classNames);
    }

    public function best(): ClassName
    {
        if (empty($this->classNames)) {
            throw new \RuntimeException(
                'There are no class name candidates'
            );
        }

        return reset($this->classNames);
    }

    private function add(ClassName $className)
    {
        $this->classNames[] = $className;
    }
}
