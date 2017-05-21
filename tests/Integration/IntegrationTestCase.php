<?php

namespace DTL\ClassFileConverter\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class IntegrationTestCase extends TestCase
{
    protected function initWorkspace()
    {
        $filesystem = new Filesystem();
        if ($filesystem->exists($this->workspacePath())) {
            $filesystem->remove($this->workspacePath());
        }

        $filesystem->mkdir($this->workspacePath());
    }

    protected function loadExample($path)
    {
        $path = __DIR__ . '/' . $path;
        $filesystem = new Filesystem();
        $filesystem->mirror($path, $this->workspacePath());
        chdir($this->workspacePath());
        exec('composer dumpautoload');
    }

    protected function workspacePath()
    {
        return __DIR__ . '/workspace';
    }
}
