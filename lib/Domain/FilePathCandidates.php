<?php

namespace DTL\ClassFileConverter\Domain;

use DTL\ClassFileConverter\Domain\FilePath;

final class FilePathCandidates implements \IteratorAggregate
{
    private $filePaths = [];

    private function __construct(array $filePaths = [])
    {
        foreach ($filePaths as $filePath) {
            $this->add($filePath);
        }
    }

    public static function create()
    {
        return new self([]);
    }

    public static function fromFilePaths(array $filePaths)
    {
        return new self($filePaths);
    }

    public function toArray(): array
    {
        return $this->filePath;
    }

    public function add(FilePath $filePath)
    {
        $this->filePaths[] = $filePath;
    }

    public function best(): FilePath
    {
        return reset($this->filePaths);
    }

    public function noneFound(): bool
    {
        return empty($this->filePaths);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->filePaths);
    }
}
