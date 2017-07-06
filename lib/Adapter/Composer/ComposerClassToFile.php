<?php

namespace Phpactor\ClassFileConverter\Adapter\Composer;

use Phpactor\ClassFileConverter\Domain\ClassToFile;
use Composer\Autoload\ClassLoader;
use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\ClassFileConverter\Domain\FilePath;
use Phpactor\ClassFileConverter\Domain\FilePathCandidates;

class ComposerClassToFile implements ClassToFile
{
    /**
     * @var ClassLoader
     */
    private $classLoader;

    public function __construct(ClassLoader $classLoader)
    {
        $this->classLoader = $classLoader;
    }

    public function classToFileCandidates(ClassName $className): FilePathCandidates
    {
        $candidates = [];
        foreach ($this->getStrategies() as $strategy) {
            list($prefixes, $inflector) = $strategy;
            $this->resolveFile($candidates, $prefixes, $inflector, $className);
        }

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
        ];
    }

    private function resolveFile(&$candidates, array $prefixes, NameInflector $inflector, ClassName $className)
    {
        list($prefix, $files) = $this->getFileCandidates($className, $prefixes);

        foreach ($files as $file) {
            $candidates[] = $inflector->inflectToRelativePath($prefix, $className, $file);
        }
    }

    private function getFileCandidates(ClassName $className, array $prefixes)
    {
        $bestFiles = [null, []];
        $bestLength = 0;

        foreach ($prefixes as $prefix => $files) {
            $files = (array) $files;
            $files = array_map(function ($file) {
                if (!file_exists($file)) {
                    throw new \RuntimeException(sprintf(
                        'Composer mapped directory "%s" does not exist', $file
                    ));
                }

                return realpath($file);
            }, $files);

            if ($className->beginsWith($prefix)) {
                $length = strlen($prefix);

                if ($length > $bestLength) {
                    $bestFiles = array($prefix, $files);
                    $bestLength = $length;
                }
            }
        }

        if (empty($bestFiles)) {
            throw new \RuntimeException(sprintf(
                'Could not find matching prefix for class name "%s", is it correctly defined in your composer file?',
                (string) $className
            ));
        }

        return $bestFiles;
    }
}
