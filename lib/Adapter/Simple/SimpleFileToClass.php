<?php

namespace Phpactor\ClassFileConverter\Adapter\Simple;

use Phpactor\ClassFileConverter\Domain\FileToClass;
use Phpactor\ClassFileConverter\Domain\ClassNameCandidates;
use Phpactor\ClassFileConverter\Domain\FilePath;
use Phpactor\ClassFileConverter\Domain\ClassName;

class SimpleFileToClass implements FileToClass
{
    public function fileToClassCandidates(FilePath $filePath): ClassNameCandidates
    {
        $classNames = [];

        $className = $this->getClassNameFromFile($filePath->__toString());

        if ($className) {
            $classNames[] = ClassName::fromString($className);
        }

        return ClassNameCandidates::fromClassNames($classNames);
    }

    /**
     * Return the class name from a file.
     *
     * Taken from http://stackoverflow.com/questions/7153000/get-class-name-from-file
     *
     * @param string $file
     *
     * @return string
     */
    private function getClassNameFromFile($file)
    {
        $fp = fopen($file, 'r');

        $class = $namespace = $buffer = '';
        $i = 0;

        while (!$class) {
            if (feof($fp)) {
                break;
            }

            // Read entire lines to prevent keyword truncation
            for ($line = 0; $line <= 20; $line++) {
                $buffer .= fgets($fp);
            }
            $tokens = @token_get_all($buffer);

            if (strpos($buffer, '{') === false) {
                continue;
            }

            for (; $i < count($tokens); $i++) {
                if ($tokens[$i][0] === T_NAMESPACE) {
                    for ($j = $i + 1; $j < count($tokens); $j++) {
                        if ($tokens[$j][0] === T_STRING) {
                            $namespace .= '\\' . $tokens[$j][1];
                        } elseif ($tokens[$j] === '{' || $tokens[$j] === ';') {
                            break;
                        }
                    }
                }

                if ($tokens[$i][0] === T_CLASS) {
                    for ($j = $i + 1; $j < count($tokens); $j++) {
                        if ($tokens[$j][0] === T_STRING) {
                            $class = $tokens[$i + 2][1];
                            break 2;
                        }
                    }
                }
            }
        }

        if (!trim($class)) {
            return;
        }

        fclose($fp);

        return ltrim($namespace . '\\' . $class, '\\');
    }
}
