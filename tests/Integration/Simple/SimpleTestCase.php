<?php

namespace Phpactor\ClassFileConverter\Tests\Integration\Simple;

use Phpactor\ClassFileConverter\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;

class SimpleTestCase extends IntegrationTestCase
{
    protected function copyProject()
    {
        $projectPath = __DIR__.'/project';
        $filesystem = new Filesystem();
        $filesystem->mirror($projectPath, $this->workspacePath());
    }
}
