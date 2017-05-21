<?php

namespace DTL\ClassFileConverter\Tests\Integration\Composer;

use DTL\ClassFileConverter\Composer\ComposerClassFileConverter;
use DTL\ClassFileConverter\FilePath;
use DTL\ClassFileConverter\ClassName;
use DTL\ClassFileConverter\Tests\Integration\IntegrationTestCase;

class ComposerClassFileConverterTest extends IntegrationTestCase
{
    public function setUp()
    {
        $this->initWorkspace();
    }

    /**
     * Using PSR-4, it should convert a class name to a file path.
     */
    public function testPsr4ClassNameToFilePath()
    {
        $this->loadExample('Composer/_examples/psr-4-project/basic');
        $this->assertEquals(
            FilePath::fromString($this->workspacePath() . '/asd'),
            $this->getConverter()->classToFile(ClassName::fromString('Acme\\Test\\Foo\\Class'))
        );
    }

    private function getConverter()
    {
        return new ComposerClassFileConverter($this->getClassLoader());
    }

    private function getClassLoader()
    {
        return require $this->workspacePath() . '/vendor/autoload.php';
    }
}
