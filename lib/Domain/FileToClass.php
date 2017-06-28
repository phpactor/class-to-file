<?php

namespace DTL\ClassFileConverter\Domain;

interface FileToClass
{
    public function fileToClassCandidates(FilePath $filePath): ClassNameCandidates;
}
