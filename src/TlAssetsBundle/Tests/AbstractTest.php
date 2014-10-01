<?php

namespace TlAssetsBundle\Tests;

abstract class AbstractTest extends  \PHPUnit_Framework_TestCase {


    const TEST_FOLDER = './src/TlAssetsBundle/Tests';
    const TMP_FOLDER = './src/TlAssetsBundle/Tests/tmp';
    protected $config;

    public function setUp()
    {
        $this->config = array('web_folder'=>self::TEST_FOLDER.'/web',
                            'buffer_folder'=>self::TMP_FOLDER.'/cache',
                            'node_folder'=>self::TMP_FOLDER.'/node_modules',
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