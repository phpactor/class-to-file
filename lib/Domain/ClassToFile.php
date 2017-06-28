<?php

namespace DTL\ClassFileConverter\Domain;

interface ClassToFile
{
    public function classToFileCandidates(ClassName $className): FilePathCandidates;
}
