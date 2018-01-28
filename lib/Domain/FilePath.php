<?php

namespace Phpactor\ClassFileConverter\Domain;

use Webmozart\PathUtil\Path;

final class FilePath
{
    private $path;

    private function __construct(string $path)
    {
        $path = Path::canonicalize($path);
        $this->path = $path;
    }

    public function isAbsolute()
    {
        return 0 === strpos($this->path, '/');
    }

    public function __toString()
    {
        return $this->path;
    }

    public static function fromString($path)
    {
        return new self($path);
    }

    public static function fromParts(array $parts)
    {
        $path = implode('/', $parts);

        return new self($path);
    }
}
