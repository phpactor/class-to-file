<?php

namespace Phpactor\ClassFileConverter\Domain;

interface ClassToFile
{
    public function classToFileCandidates(ClassName $className): FilePathCandidates;
}
