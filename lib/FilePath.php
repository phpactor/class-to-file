<?php

namespace DTL\ClassFileConverter;

final class FilePath
{
    private $path;

    private function __construct(string $path)
    {
        $this->path = $path;
    }

    public static function fromString($string)
    {
        return new self($string);
    }

    public static function fromParts(array $parts)
    {
        $path = implode('/', $parts);

        return new self($path);
    }
}
