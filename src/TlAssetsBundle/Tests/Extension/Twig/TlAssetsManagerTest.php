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
                    array('name'=>'1dea999',
                          'files'=>array(array('src'=>getcwd().'/src/TlAssetsBundle/Tests/web/bundles/testbundle/js/main.js',
                                               'dest'=>'/public/js/1dea999_part1_main.js')),
                          'type'=>'javascript',
                          'rootWebPath'=>'./src/TlAssetsBundle/Tests/web',
                          'filters'=>array()
                    )
                ),
                array(
                    array('/bundles/testbundle/less/'),
                    array('filters'=>array('less')),
                    'style',
                    array('name'=>'af06088',
                          'files'=>array(array('src'=>getcwd().'/src/TlAssetsBundle/Tests/web/bundles/testbundle/less/style.less',
                                               'dest'=>'/public/css/af06088_part1_style.css')),
                          'type'=>'stylesheet',
                          'rootWebPath'=>'./src/TlAssetsBundle/Tests/web',
                          'filters'=>array('less')
                    )
                ),
                array(
                    array('/bundles/testbundle/less/'),
                    array('filters'=>array('less','concat')),
                    'style',
                    array('name'=>'733207e',
                        'files'=>array(array('src'=>getcwd().'/src/TlAssetsBundle/Tests/web/bundles/testbundle/less/style.less',
                                             'dest'=>'/public/css/733207e_part1_style.css')),
                        'type'=>'stylesheet',
                        'concatDest'=>'/public/css/733207e.css',
                        'rootWebPath'=>'./src/TlAssetsBundle/Tests/web',
                        'filters'=>array('less','concat')
                    )
                )
        );
    }

    /**
     * @dataProvider dataProviderForBuildBuffer
     */
    public function testBuildBuffer($inputs, $attributes, $tag, $expected)
    {
        $tlAssetsManager = new TlAssetsManager(self::TEST_DIR,self::TEST_DIR.'/cache/',false,false,false,array());
        $tlAssetsManager->load($inputs, $attributes,$tag) ;

        $this->assertFileExists(self::TEST_DIR.'/cache/tlassets/buffer/'.$expected['name'].'.json');
        $actual = file_get_contents(self::TEST_DIR.'/cache/tlassets/buffer/'.$expected['name'].'.json');
        $actualData = json_decode($actual,true);

        $this->assertEquals($expected, $actualData);
    }

    public function tearDown()
    {
//        if(file_exists(self::TEST_DIR.'/cache/')) {
//            $this->_remove(self::TEST_DIR.'/cache/');
//        }
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