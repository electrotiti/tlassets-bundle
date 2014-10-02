<?php

namespace TlAssetsBundle\Tests;

use TlAssetsBundle\Tests\AbstractTest;

class GlobalTearDown extends AbstractTest
{
    public function testTearDown()
    {
        if(file_exists(getcwd().'/src/TlAssetsBundle/Tests/tmp/')) {
            $this->_remove(getcwd().'/src/TlAssetsBundle/Tests/tmp/');
        }

        if(file_exists(getcwd().'/src/TlAssetsBundle/Tests/node_modules/')) {
            $this->_remove(getcwd().'/src/TlAssetsBundle/Tests/node_modules/');
        }
    }
}