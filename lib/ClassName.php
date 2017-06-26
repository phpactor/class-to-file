<?php

namespace DTL\ClassFileConverter;

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
        return substr($this->fullyQualifiedName, 0, strrpos($this->fullyQualifiedName, '\\'));
    }

    public function name()
    {
        return substr($this->fullyQualifiedName, strrpos($this->fullyQualifiedName, '\\') + 1);
    }

    public function beginsWith($prefix)
    {
        return 0 === strpos($this->fullyQualifiedName, $prefix);
    }
}
