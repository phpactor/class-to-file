<?php

namespace DTL\ClassFileConverter;

use DTL\ClassFileConverter\FilePath;

final class FilePathCandidates implements \IteratorAggregate
{
    private $filePaths;

    public static function create()
    {
        return new self();
    }

    public static function fromFilePaths(array $filePaths)
    {
        return new self($filePaths);
    }

    public function add(FilePath $filePath)
    {
        $new = new self();
        $new->filePaths[] = $filePath;

        return $new;
    }

    public function getIterator()
    {
        return $this->filePaths;
    }

    public function notEmpty(): bool
    {
        return !empty($this->filePaths);
    }
}
