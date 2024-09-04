<?php

namespace Phpactor\ClassFileConverter\Adapter\Composer;

use Composer\Autoload\ClassLoader;
use Phpactor\ClassFileConverter\Domain\FilePath;
use Phpactor\ClassFileConverter\Domain\FileToClass;
use Phpactor\ClassFileConverter\Domain\ClassNameCandidates;
use InvalidArgumentException;
use Symfony\Component\Filesystem\Path;

final class ComposerFileToClass implements FileToClass
{
    /**
     * @var ClassLoader
     */
    private $classLoader;

    public function __construct(ClassLoader $classLoader)
    {
        $this->classLoader = $classLoader;
    }

    public function fileToClassCandidates(FilePath $filePath): ClassNameCandidates
    {
        if (false === $filePath->isAbsolute()) {
            throw new InvalidArgumentException(sprintf(
                'Path must be absolute'
            ));
        }

        $classNames = [];

        foreach ($this->getStrategies() as $strategy) {
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
                $this->classLoader->getClassMap(),
                new ClassmapNameInflector(),
            ],
            [
                $this->classLoader->getPrefixesPsr4(),
                new Psr4NameInflector(),
            ],
            [
                $this->classLoader->getPrefixes(),
                new Psr0NameInflector(),
            ],
            [
                $this->classLoader->getFallbackDirs(),
                new Psr0NameInflector(),
            ],
            [
                $this->classLoader->getFallbackDirsPsr4(),
                new Psr4NameInflector(),
            ],
        ];
    }

    private function populateCandidates(FilePath $filePath, array $prefixes)
    {
        $candidates = [];
        foreach ($prefixes as $classPrefix => $pathPrefixes) {
            $pathPrefixes = (array) $pathPrefixes;

            // remove any relativeness from the paths
            // we should not depend on the file path existing.
            $pathPrefixes = array_map(function ($pathPrefix) {
                $canonicalizedPath = Path::canonicalize($pathPrefix);
                $realPath = realpath($canonicalizedPath);
                return $realPath ?: $canonicalizedPath;
            }, $pathPrefixes);

            foreach ($pathPrefixes as $pathPrefix) {
                if ((string) $filePath == $pathPrefix) {
                    $candidates[] = [ $pathPrefix, $classPrefix ];
                    continue;
                }


                if (strpos($filePath, $pathPrefix) !== 0) {
                    continue;
                }

                $candidates[] = [
                    $pathPrefix,
                    $classPrefix,
                ];
            }
        }

        usort($candidates, function (array $leftCandidate, array $rightCandidate): int {
            return strlen($rightCandidate[0]) <=> strlen($leftCandidate[0]);
        });

        return $candidates;
    }
}
