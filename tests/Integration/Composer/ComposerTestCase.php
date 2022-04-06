<?php

namespace Phpactor\ClassFileConverter\Tests\Integration\Composer;

use Composer\Autoload\ClassLoader;
use Phpactor\ClassFileConverter\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @runTestsInSeparateProcesses
 */
abstract class ComposerTestCase extends IntegrationTestCase
{
    public function setUp(): void
    {
        $this->initWorkspace();
    }

    protected function loadExample($composerFile): void
    {
        $projectPath = __DIR__.'/project';
        $composerPath = __DIR__.'/composers/'.$composerFile;
        $filesystem = new Filesystem();
        $filesystem->mirror($projectPath, $this->workspacePath());
        $filesystem->copy($composerPath, $this->workspacePath().'/composer.json');
        chdir($this->workspacePath());
        exec('composer install 2> /dev/null');
    }

    protected function getClassLoader(): ClassLoader
    {
        return require $this->workspacePath().'/vendor/autoload.php';
    }
}
