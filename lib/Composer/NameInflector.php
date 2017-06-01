<?php

namespace DTL\ClassFileConverter\Composer;

use DTL\ClassFileConverter\ClassName;
use DTL\ClassFileConverter\FilePath;

interface NameInflector
{
    public function inflectToRelativePath(string $prefix, ClassName $className): string;

    public function inflectToClassName(FilePath $filePath, string $pathPrefix, string $classPrefix): ClassName;
}
