<?php

namespace Phpactor\ClassFileConverter\Adapter\Composer;

use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\ClassFileConverter\Domain\FilePath;

final class Psr0NameInflector implements NameInflector
{
    public const SEPARATOR = '_';

    public function inflectToRelativePath(string $prefix, ClassName $className, string $mappedPath): FilePath
    {
        if (substr($prefix, -1) === self::SEPARATOR && $className->beginsWith($prefix, self::SEPARATOR)) {
            $elements = explode(self::SEPARATOR, $className);
            $className = implode('\\', $elements);
        }

        $relativePath = str_replace('\\', '/', (string) $className).'.php';

        return FilePath::fromParts([$mappedPath, $relativePath]);
    }

    public function inflectToClassName(FilePath $filePath, string $pathPrefix, string $classPrefix): ClassName
    {
        $className = substr($filePath, strlen($pathPrefix) + 1);
        $className = str_replace('/', '\\', $className);
        $className = preg_replace('{\.(.+)$}', '', $className);

        return ClassName::fromString($className);
    }
}
