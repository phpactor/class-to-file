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

        $classNames = [];

        foreach ($this->getStrategies() as $prefixes => $strategy) {
            list($prefixes, $inflector) = $strategy;
            $candidates = $this->populateCandidates($filePath, $prefixes);

            foreach ($candidates as $candidate) {
                list($pathPrefix, $classPrefix) = $candidate;
                $classNames[] = $inflector->inflectToClassName($filePath, $pathPrefix, $classPrefix);
            }
        }

        return ClassNameCandidates::fromClassNames($classNames);
    }

    private function getStrategies(): array
    {
        return [
            [
                $this->classLoader->getPrefixesPsr4(),
                new Psr4NameInflector()
            ],
            [
                $this->classLoader->getPrefixes(),
                new Psr0NameInflector(),
            ]
        ];
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
