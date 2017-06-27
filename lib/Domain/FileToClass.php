<?php

namespace DTL\ClassFileConverter\Domain;

use DTL\ClassFileConverter\Domain\ClassNameCandidates;
use DTL\ClassFileConverter\Domain\FilePath;

interface FileToClass
{
    public function fileToClass(FilePath $filePath): ClassNameCandidates;
}
