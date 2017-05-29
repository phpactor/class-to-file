<?php

namespace DTL\ClassFileConverter;

interface ClassToFile
{
    public function classToFile(ClassName $className): FilePathCandidates;
}
