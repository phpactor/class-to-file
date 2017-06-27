<?php

namespace DTL\ClassFileConverter\Domain;

use DTL\ClassFileConverter\Domain\ClassName;

interface ClassToFile
{
    public function classToFileCandidates(ClassName $className): FilePathCandidates;
}
