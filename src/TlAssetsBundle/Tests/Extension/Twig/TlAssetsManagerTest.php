<?php

namespace TlAssetsBundle\Tests\Extension\Twig;

use TlAssetsBundle\Extension\Twig\TlAssetsManager;
use TlAssetsBundle\Tests\AbstractTest;

class TlAssetsManagerTest extends AbstractTest
{

    public function dataProviderForBuildBuffer()
    {
        return array(
                array(
                    array('/bundles/test/js/01-script.js'),
                    array('filters'=>array()),
                    'js',
                    array('name'=>'b588f9c',
                          'files'=>array(array('src'=>getcwd().'/src/TlAssetsBundle/Tests/web/bundles/test/js/01-script.js',
                                               'dest'=>'/public/js/b588f9c_part1_01-script.js')),
                          'type'=>'javascript',
                          'rootWebPath'=>getcwd().'/src/TlAssetsBundle/Tests/web',
                          'filters'=>array()
                    )
                ),
//                array(
//                    array('/bundles/test/less/style1.less'),
//                    array('filters'=>array('less')),
//                    'style',
//                    array('name'=>'2bb9c62',
//                          'files'=>array(array('src'=>getcwd().'/src/TlAssetsBundle/Tests/web/bundles/test/less/01-style.less',
//                                               'dest'=>'/public/css/2bb9c62_part1_01-style.css')),
//                          'type'=>'stylesheet',
//                          'rootWebPath'=>getcwd().'/src/TlAssetsBundle/Tests/web',
//                          'filters'=>array('less')
//                    )
//                ),
//                array(
//                    array('/bundles/test/less/'),
//                    array('filters'=>array('less','concat')),
//                    'style',
//                    array('name'=>'311b5b6',
//                        'files'=>array(
//                                    array('src'=>getcwd().'/src/TlAssetsBundle/Tests/web/bundles/test/less/01-style.less'),
//                                    array('src'=>getcwd().'/src/TlAssetsBundle/Tests/web/bundles/test/less/02-style.less'),
//                                    ),
//                        'type'=>'stylesheet',
//                        'concatDest'=>'/public/css/311b5b6.css',
//                        'rootWebPath'=>getcwd().'/src/TlAssetsBundle/Tests/web',
//                        'filters'=>array('less','concat')
//
//                    )
//                )
        );
    }

    /**
     * @dataProvider dataProviderForBuildBuffer
     */
    public function testBuildBuffer($inputs, $attributes, $tag, $expected)
    {
        $tlAssetsManager = new TlAssetsManager($this->config);
        $tlAssetsManager->load($inputs, $attributes,$tag) ;

        $this->assertFileExists($this->config['buffer_folder'].'/'.$expected['name'].'.json');
        $actual = file_get_contents($this->config['buffer_folder'].'/'.$expected['name'].'.json');
        $actualData = json_decode($actual,true);

        $this->assertEquals($expected, $actualData);
    }
}