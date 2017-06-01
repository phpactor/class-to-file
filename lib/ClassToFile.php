<?php

namespace DTL\ClassFileConverter;

interface ClassToFile
{
    public function classToFileCandidates(ClassName $className): FilePathCandidates;
}
