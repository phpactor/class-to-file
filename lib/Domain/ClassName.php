<?php

namespace Phpactor\ClassFileConverter\Domain;

final class ClassName
{
    private $fullyQualifiedName;

    private function __construct()
    {
    }

    public static function fromString($string)
    {
        $new = new self();
        $new->fullyQualifiedName = $string;

        return $new;
    }

    public function __toString()
    {
        return $this->fullyQualifiedName;
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

    public function beginsWith($prefix)
    {
        return 0 === strpos($this->fullyQualifiedName, $prefix);
    }
}
