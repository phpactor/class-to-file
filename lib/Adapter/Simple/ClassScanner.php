<?php

namespace Phpactor\ClassFileConverter\Adapter\Simple;

use RuntimeException;

/**
 * Return the class name from a file.
 *
 * Based on http://stackoverflow.com/questions/7153000/get-class-name-from-file
 */
class ClassScanner
{
    public function getClassNameFromFile(string $file): ?string
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
            $tokens = @\token_get_all($buffer);

            if (strpos($buffer, '{') === false) {
                continue;
            }

            for (; $i < count($tokens); $i++) {
                if ($tokens[$i][0] === T_NAMESPACE) {
                    for ($j = $i + 1; $j < count($tokens); $j++) {
                        $tokenId = $tokens[$j][0];
                        $namespaceToken = defined('T_NAME_QUALIFIED') ? T_NAME_QUALIFIED : T_STRING;

                        if ($tokenId === T_STRING || $tokenId === $namespaceToken) {
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
            return null;
        }

        return $namespace . '\\' . $class;
    }
}
