<?php

namespace DTL\ClassFileConverter\Composer;

use DTL\ClassFileConverter\ClassName;

interface NameInflector
{
    public function inflectToRelativePath(string $prefix, ClassName $className): string;
}
