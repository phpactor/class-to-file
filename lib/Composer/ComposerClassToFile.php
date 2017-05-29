<?php

namespace DTL\ClassFileConverter\Composer;

use DTL\ClassFileConverter\ClassToFile;
use Composer\Autoload\ClassLoader;
use DTL\ClassFileConverter\ClassName;
use DTL\ClassFileConverter\FilePath;
use DTL\ClassFileConverter\FilePathCandidates;

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

    public function classToFile(ClassName $className): FilePathCandidates
    {
        $candidates = FilePathCandidates::create();

        foreach ($this->getStrategies() as $strategy) {
            list($prefixes, $inflector) = $strategy;
            $this->resolveFile($candidates, $prefixes, $inflector, $className);
        }

        return $candidates;
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

    private function resolveFile(FilePathCandidates $candidates, array $prefixes, NameInflector $inflector, ClassName $className)
    {
        list($prefix, $files) = $this->getFileCandidates($className, $prefixes);

        $filePaths = [];
        foreach ($files as $file) {
            $relPath = $inflector->inflectToRelativePath($prefix, $className);
            $candidates->add(FilePath::fromParts([ $file, $relPath ]));
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
