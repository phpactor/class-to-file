<?php

namespace DTL\ClassFileConverter;

use DTL\ClassFileConverter\ClassName;

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
        return reset($this->classNames);
    }

    private function add(ClassName $className)
    {
        $this->classNames[] = $className;
    }
}
