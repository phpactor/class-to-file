<?php

namespace DTL\ClassFileConverter\Adapter\Composer;

use DTL\ClassFileConverter\Domain\ClassName;
use DTL\ClassFileConverter\Domain\FilePath;

interface NameInflector
{
    public function inflectToRelativePath(string $prefix, ClassName $className, string $mappedPath): FilePath;

    public function inflectToClassName(FilePath $filePath, string $pathPrefix, string $classPrefix): ClassName;
}
