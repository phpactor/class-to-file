<?php

namespace DTL\ClassFileConverter\Composer;

use Composer\Autoload\ClassLoader;
use DTL\ClassFileConverter\ClassName;
use DTL\ClassFileConverter\FilePath;
use DTL\ClassFileConverter\ClassToFile;
use DTL\ClassFileConverter\FileToClass;
use DTL\ClassFileConverter\FilePathCandidates;
use DTL\ClassFileConverter\ClassNameCandidates;

final class ComposerFileToClass implements FileToClass
{
    /**
     * @var ClassLoader
     */
    private $classLoader;

    public function __construct(ClassLoader $classLoader)
    {
        $this->classLoader = $classLoader;
        $reflection = new \ReflectionClass($classLoader);
    }

    public function fileToClass(FilePath $filePath): ClassNameCandidates
    {
        if (false === $filePath->isAbsolute()) {
            throw new \InvalidArgumentException(sprintf(
                'Path must be absolute'
            ));
        }

        $psr4Candidates = $this->populateCandidates($filePath, $this->classLoader->getPrefixesPsr4());
        $psr0Candidates = $this->populateCandidates($filePath, $this->classLoader->getPrefixes());

        $candidates = array_merge($psr4Candidates, $psr0Candidates);

        usort($candidates, function ($candidateA, $candidateB) {
            return -(strlen($candidateA[0]) <=> strlen($candidateB[0]));
        });

        $classNames = [];
        foreach ($candidates as $candidate) {
            list($pathPrefix, $classPrefix) = $candidate;
            $classNames[] = (new Psr4NameInflector())->inflectToClassName($filePath, $pathPrefix, $classPrefix);
        }

        return ClassNameCandidates::fromClassNames($classNames);
    }

    private function populateCandidates(FilePath $filePath, array $prefixes)
    {
        $candidates = [];
        foreach ($prefixes as $classPrefix => $pathPrefixes) {
            $pathPrefixes = (array) $pathPrefixes;

            // remove any relativeness from the paths
            //
            // TODO: realpath will return void if the path does not exist
            //       we should not depend on the file path existing.
            $pathPrefixes = array_map(function ($pathPrefix) {
                return realpath($pathPrefix);
            }, $pathPrefixes);

            foreach ($pathPrefixes as $pathPrefix) {
                if (strpos($filePath, $pathPrefix) !== 0) {
                    continue;
                }

                $candidates[] = [
                    $pathPrefix,
                    $classPrefix,
                ];
            }
        }

        return $candidates;
    }
}
