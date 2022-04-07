<?php

namespace Phpactor\ClassFileConverter\Adapter\Composer;

use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\ClassFileConverter\Domain\FilePath;

final class Psr4NameInflector implements NameInflector
{
    public const NAMESPACE_SEPARATOR = ClassName::DEFAULT_NAMESPACE_SEPARATOR;

    public function inflectToRelativePath(string $prefix, ClassName $className, string $mappedPath): FilePath
    {
        $relativePath = str_replace(self::NAMESPACE_SEPARATOR, '/', substr($className, strlen($prefix))).'.php';

        return FilePath::fromParts([$mappedPath, $relativePath]);
    }

    public function inflectToClassName(FilePath $filePath, string $pathPrefix, string $classPrefix): ClassName
    {
        // class prefix is "0" when prefix is "" (fallback) in composer.json
        if ('0' == (string) $classPrefix) {
            $classPrefix = '';
        }

        $className = substr($filePath, strlen($pathPrefix) + 1);
        $className = str_replace('/', self::NAMESPACE_SEPARATOR, $className);
        $className = $classPrefix.$className;
        $className = preg_replace('{\.(.+)$}', '', $className);

        return ClassName::fromString($className);
    }
}
