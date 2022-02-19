<?php

namespace Phpactor\ClassFileConverter\Domain;

use IteratorAggregate;
use RuntimeException;
use ArrayIterator;
use Traversable;

final class FilePathCandidates implements IteratorAggregate
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
        return $this->filePaths;
    }

    public function add(FilePath $filePath): void
    {
        $this->filePaths[] = $filePath;
    }

    public function best(): FilePath
    {
        if (empty($this->filePaths)) {
            throw new RuntimeException(
                'There are no file path candidates'
            );
        }

        return reset($this->filePaths);
    }

    public function noneFound(): bool
    {
        return empty($this->filePaths);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->filePaths);
    }
}
