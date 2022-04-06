<?php

namespace Phpactor\ClassFileConverter\Domain;

final class ClassName
{
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
        return substr($this->fullyQualifiedName, 0, (int) strrpos($this->fullyQualifiedName, '\\'));
    }

    public function name()
    {
        $pos = strrpos($this->fullyQualifiedName, '\\');
        if (false === $pos) {
            return $this->fullyQualifiedName;
        }
        return substr($this->fullyQualifiedName, $pos + 1);
    }

    public function beginsWith(string $prefix, string $separator = '\\'): bool
    {
        if ($prefix === $this->fullyQualifiedName) {
            return true;
        }

        if (0 !== strpos($this->fullyQualifiedName, $prefix)) {
            return false;
        }

        if (mb_substr($prefix, -1, 1) === $separator) {
            return true;
        }

        return mb_substr($this->fullyQualifiedName, mb_strlen($prefix), 1) === $separator;
    }
}
