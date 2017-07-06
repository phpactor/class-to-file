<?php

namespace Phpactor\ClassFileConverter\Domain;

interface FileToClass
{
    public function fileToClassCandidates(FilePath $filePath): ClassNameCandidates;
}
