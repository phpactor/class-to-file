<?php

namespace DTL\ClassFileConverter\Composer;

use DTL\ClassFileConverter\ClassName;

class Psr4NameInflector implements NameInflector
{
    public function inflectToRelativePath(string $prefix, ClassName $className): string
    {
        return str_replace('\\', '/', substr($className, strlen($prefix))) . '.php';
    }
}
