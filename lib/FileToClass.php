<?php

namespace DTL\ClassFileConverter;

interface FileToClass
{
    public function fileToClass(FilePath $filePath): ClassNameCandidates;
}
