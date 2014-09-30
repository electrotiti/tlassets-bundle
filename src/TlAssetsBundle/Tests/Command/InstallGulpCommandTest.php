<?php

namespace TlAssetsBundle\Tests\Command;

use TlAssetsBundle\Command\InstallGulpCommand;

class InstallGulpCommandTest extends \PHPUnit_Framework_TestCase
{
    const TEST_FOLDER = '/src/TlAssetsBundle/Tests';

    public function testGulpInstall()
    {
        $reflection = new \ReflectionClass('TlAssetsBundle\Command\InstallGulpCommand');
        $method = $reflection->getMethod('_installGulp');
        $method->setAccessible(true);

        $rootDir = getcwd();
        $gulpSrcFolder = $rootDir.'/src/TlAssetsBundle/Compiler/Gulp/';
        $nodeDestFolder = $rootDir.'/src/TlAssetsBundle/Tests/';

        $result = $method->invokeArgs(new InstallGulpCommand(), array($rootDir,$gulpSrcFolder, $nodeDestFolder));

        $this->assertEquals(0,$result,'Some errors occurred during installation of GULP');
        $this->fileExists($rootDir.'/src/TlAssetsBundle/Tests/node_modules/tlassets-bundle/');

    }
}