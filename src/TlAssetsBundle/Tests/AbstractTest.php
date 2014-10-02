<?php

namespace TlAssetsBundle\Tests;

abstract class AbstractTest extends  \PHPUnit_Framework_TestCase
{

    protected $config;

    public function setUp()
    {
        $this->config = array('web_folder'=>getcwd().'/src/TlAssetsBundle/Tests/web/',
                              'buffer_folder'=>getcwd().'/src/TlAssetsBundle/Tests/tmp/cache',
                              'node_folder'=>getcwd().'/src/TlAssetsBundle/Tests/tmp/node_modules',
                              'public_folder'=>'/public');
    }

    protected function _remove($path)
    {
        if (is_dir($path) === true)
        {
            $files = array_diff(scandir($path), array('.', '..'));

            foreach ($files as $file)
            {
                $this->_remove(realpath($path) . '/' . $file);
            }

            return rmdir($path);
        }

        else if (is_file($path) === true)
        {
            return unlink($path);
        }

        return false;
    }

}