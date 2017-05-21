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
}
