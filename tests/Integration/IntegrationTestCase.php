<?php

namespace Phpactor\ClassFileConverter\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

abstract class IntegrationTestCase extends TestCase
{
    protected function initWorkspace()
    {
        $filesystem = new Filesystem();
        if ($filesystem->exists($this->workspacePath())) {
            $filesystem->remove($this->workspacePath());
        }

        $filesystem->mkdir($this->workspacePath());
    }

    protected function workspacePath()
    {
        return __DIR__.'/../Workspace';
    }
}
