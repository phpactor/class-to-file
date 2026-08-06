<?php

namespace Phpactor\ClassFileConverter\Adapter\Composer;

use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\ClassFileConverter\Domain\FilePath;

final class ClassmapNameInflector implements NameInflector
{
    public const NAMESPACE_SEPARATOR = ClassName::DEFAULT_NAMESPACE_SEPARATOR;

    public function inflectToRelativePath(string $prefix, ClassName $className, string $mappedPath): FilePath
    {
        return FilePath::fromString($mappedPath);
    }

    public function inflectToClassName(FilePath $filePath, string $pathPrefix, string $classPrefix): ClassName
    {
        return ClassName::fromString($classPrefix);
    }

    public function getNamespaceSeparator(): string
    {
        return self::NAMESPACE_SEPARATOR;
    }
}
