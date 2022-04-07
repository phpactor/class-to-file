<?php

namespace Phpactor\ClassFileConverter\Domain;

use function in_array;

final class ClassName
{
    public const DEFAULT_NAMESPACE_SEPARATOR = '\\';

    private $fullyQualifiedName;

    private function __construct()
    {
    }

    public function __toString()
    {
        return $this->fullyQualifiedName;
    }

    public static function fromString($string)
    {
        $new = new self();
        $new->fullyQualifiedName = $string;

        return $new;
    }

    public function namespace()
    {
        return substr($this->fullyQualifiedName, 0, (int) strrpos(
            $this->fullyQualifiedName,
            self::DEFAULT_NAMESPACE_SEPARATOR,
        ));
    }

    public function name()
    {
        $pos = strrpos($this->fullyQualifiedName, self::DEFAULT_NAMESPACE_SEPARATOR);
        if (false === $pos) {
            return $this->fullyQualifiedName;
        }
        return substr($this->fullyQualifiedName, $pos + 1);
    }

    public function beginsWith(string $prefix, string $additionalNseparator = self::DEFAULT_NAMESPACE_SEPARATOR): bool
    {
        if ($prefix === $this->fullyQualifiedName) {
            return true;
        }

        if (0 !== strpos($this->fullyQualifiedName, $prefix)) {
            return false;
        }

        if ($this->isNamespaceSeparator(mb_substr($prefix, -1, 1), $additionalNseparator)) {
            return true;
        }

        return mb_substr($this->fullyQualifiedName, mb_strlen($prefix), 1) === $additionalNseparator;
    }

    private function isNamespaceSeparator(
        string $character,
        string $additionalNseparator = self::DEFAULT_NAMESPACE_SEPARATOR,
    ): bool {
        return in_array($character, [self::DEFAULT_NAMESPACE_SEPARATOR, $additionalNseparator], true);
    }
}
