<?php
//// TODO Figure out why KERNEL_DIR is not read on phpunit.xml...
//
//use TlAssetsBundle\Tests\Command\CommandTestCase;
//
//class InstallGulpCommandTest extends CommandTestCase
//{
//    public function testInstallGulp()
//    {
//        $client = self::createClient();
//        $output = $this->runCommand($client, "tlassets:install:gulp");
//
//
//        $this->assertContains('Gulp and dependencies successfully installed.', $output);
//    }
//}