<?php

namespace Phpactor\ClassFileConverter\Adapter\Composer;

use Phpactor\ClassFileConverter\Domain\ClassToFile;
use Composer\Autoload\ClassLoader;
use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\ClassFileConverter\Domain\FilePathCandidates;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ComposerClassToFile implements ClassToFile
{
    /**
     * @var ClassLoader
     */
    private $classLoader;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ClassLoader $classLoader, LoggerInterface $logger = null)
    {
        $this->classLoader = $classLoader;
        $this->logger = $logger ?: new NullLogger();
    }

    public function classToFileCandidates(ClassName $className): FilePathCandidates
    {
        $candidates = [];
        foreach ($this->getStrategies() as $strategy) {
            list($prefixes, $inflector) = $strategy;
            $this->resolveFile($candidates, $prefixes, $inflector, $className);
        }

        // order with the longest prefixes first
        uksort($candidates, function ($prefix1, $prefix2) {
            return strlen($prefix2) <=> strlen($prefix1);
        });

        // flatten to a single array
        $candidates = array_reduce($candidates, function ($candidates, $paths) {
            return array_merge($candidates, $paths);
        }, []);

        return FilePathCandidates::fromFilePaths($candidates);
    }

    private function getStrategies(): array
    {
        return [
            [
                $this->classLoader->getPrefixesPsr4(),
                new Psr4NameInflector(),
            ],
            [
                $this->classLoader->getPrefixes(),
                new Psr0NameInflector(),
            ],
            [
                $this->classLoader->getClassMap(),
                new ClassmapNameInflector(),
            ],
            [
                $this->classLoader->getFallbackDirs(),
                new Psr0NameInflector(),
            ],
            [
                $this->classLoader->getFallbackDirsPsr4(),
                // PSR0 name inflector works here as there is no prefix
                new Psr0NameInflector(),
            ],
        ];
    }

    private function resolveFile(&$candidates, array $prefixes, NameInflector $inflector, ClassName $className)
    {
        $fileCandidates = $this->getFileCandidates($className, $prefixes);

        foreach ($fileCandidates as $prefix => $files) {
            $prefixCandidates = [];
            foreach ($files as $file) {
                $prefixCandidates[] = $inflector->inflectToRelativePath($prefix, $className, $file);
            }

            if (!isset($candidates[$prefix])) {
                $candidates[$prefix] = [];
            }

            $candidates[$prefix] = array_merge($candidates[$prefix], $prefixCandidates);
        }
    }

    private function getFileCandidates(ClassName $className, array $prefixes)
    {
        $candidates = [];

        foreach ($prefixes as $prefix => $paths) {
            $paths = (array) $paths;
            $paths = array_map(function ($path) {
                if (!file_exists($path)) {
                    $this->logger->warning(sprintf(
                        'Composer mapped path "%s" does not exist',
                        $path
                    ));

                    return $path;
                }

                return realpath($path);
            }, $paths);

            if (is_int($prefix)) {
                $prefix = '';
            }

            if ($prefix && false === $className->beginsWith($prefix)) {
                continue;
            }

            if (!isset($candidates[$prefix])) {
                $candidates[$prefix] = [];
            }

            $candidates[$prefix] = array_merge($candidates[$prefix], $paths);
        }

        return $candidates;
    }
}
