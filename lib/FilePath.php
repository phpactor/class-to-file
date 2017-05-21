<?php

namespace DTL\ClassFileConverter;

final class FilePath
{
    private $path;

    private function __construct()
    {
    }

    public static function fromString($string)
    {
        $new = new self();
        $new->path = $string;

        return $new;
    }
}
