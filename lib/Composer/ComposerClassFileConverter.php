<?php

namespace DTL\ClassFileConverter\Composer;

use Composer\Autoload\ClassLoader;
use DTL\ClassFileConverter\ClassName;
use DTL\ClassFileConverter\FilePath;
use DTL\ClassFileConverter\ClassToFile;
use DTL\ClassFileConverter\FileToClass;
use DTL\ClassFileConverter\FilePathCandidates;

final class ComposerClassFileConverter implements ClassToFile, FileToClass
{
    /**
     * @var ClassLoader
     */
    private $classLoader;

    public function __construct(ClassLoader $classLoader)
    {
        $this->classLoader = $classLoader;
        $this->classToFile = new ComposerClassToFile($classLoader);
    }

    public function classToFile(ClassName $className): FilePathCandidates
    {
        return $this->classToFile->classToFile($className);
    }

    public function fileToClass(FilePath $filePath): ClassName
    {
        if ($filePath->isAbsolute()) {
            throw new \InvalidArgumentException(sprintf(
                'Do not support absolute paths.'
            ));
        }

        $prefixes = array_merge(
            $this->classLoader->getPrefixes(),
            $this->classLoader->getPrefixesPsr4(),
            $this->classLoader->getClassMap()
        );

        $map = [];

        $cwd = getcwd() . '/';

        $bestLength = $base = $basePath = null;
        $isExact = false;

        foreach ($prefixes as $prefix => $files) {
            if (is_string($files)) {
                $files = [ $files ];
            }

            foreach ($files as $file) {
                $path = str_replace($cwd, '', realpath($file));

                if (strpos($filePath->getPath(), $path) === 0) {
                    if (null !== $bestLength && strlen($path) < $bestLength) {
                        continue;
                    }

                    $base = $prefix;
                    $basePath = $path;
                    $bestLength = strlen($path);

                    if ($filePath->getPath() === $path) {
                        $isExact = true;
                        break 2; // we are done here
                    }
                }
            }
        }

        if (null === $base) {
            throw new \RuntimeException(sprintf(
                'Could not resolve base path from Composer autoloader'
            ));
        }

        if (false === $isExact && substr($base, -1) !== '\\') {
            $base .= '\\';
        }

        $className = substr($filePath->getPath(), strlen($basePath) + 1);
        $className = str_replace('/', '\\', $className);
        $className = $base . $className;
        $className = preg_replace('{\.(.+)$}', '', $className);

        return ClassName::fromString($className);
    }
}
