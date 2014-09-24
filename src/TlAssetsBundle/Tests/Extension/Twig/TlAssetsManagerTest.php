<?php

namespace TlAssetsBundle\Tests\Extension\Twig;

use TlAssetsBundle\Extension\Twig\TlAssetsManager;

class TlAssetsManagerTest extends \PHPUnit_Framework_TestCase
{
    const TEST_DIR = './src/TlAssetsBundle/Tests';


    public function dataProviderForBuildBuffer() {
        return array(
                array(
                    array('/bundles/testbundle/js/'),
                    array('filters'=>array()),
                    'js',
                    '1dea999'
                ),
                array(
                    array('/bundles/testbundle/less/'),
                    array('filters'=>array('less')),
                    'style',
                    'af06088'
                )
        );
    }

    /**
     * @dataProvider dataProviderForBuildBuffer
     */
    public function testBuildBuffer($inputs, $attributes, $tag, $fileExpected)
    {
        $tlAssetsManager = new TlAssetsManager(self::TEST_DIR,self::TEST_DIR.'/cache/',false,false,false,array());
        $tlAssetsManager->load($inputs, $attributes,$tag) ;

        $this->assertFileExists(self::TEST_DIR.'/cache/tlassets/buffer/'.$fileExpected.'.json');

        $expected = file_get_contents(self::TEST_DIR.'/cache/tlassets/buffer/'.$fileExpected.'.json');
        $actual = file_get_contents(self::TEST_DIR.'/Extension/Twig/buffer/'.$fileExpected.'.json');

        $this->assertEquals($expected, $actual);
    }

    public function tearDown()
    {
        if(file_exists(self::TEST_DIR.'/cache/')) {
            $this->_remove(self::TEST_DIR.'/cache/');
        }
    }

    private function _remove($path)
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