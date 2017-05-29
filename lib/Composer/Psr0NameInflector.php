<?php

namespace DTL\ClassFileConverter\Composer;

use DTL\ClassFileConverter\ClassName;

class Psr0NameInflector implements NameInflector
{
    public function inflectToRelativePath(string $prefix, ClassName $className): string
    {
        return str_replace('\\', '/', $className) . '.php';
    }
}
