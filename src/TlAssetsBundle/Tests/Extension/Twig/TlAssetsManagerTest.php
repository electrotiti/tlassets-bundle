<?php

namespace TlAssetsBundle\Tests\Extension\Twig;

use TlAssetsBundle\Extension\Twig\TlAssetsManager;
use TlAssetsBundle\Tests\AbstractTest;

class TlAssetsManagerTest extends AbstractTest
{

    public function dataProviderForBuildBuffer() {
        return array(
                array(
                    array('/bundles/test/js/'),
                    array('filters'=>array()),
                    'js',
                    array('name'=>'2010223',
                          'files'=>array(array('src'=>getcwd().'/src/TlAssetsBundle/Tests/web/bundles/test/js/main.js',
                                               'dest'=>'/public/js/2010223_part1_main.js')),
                          'type'=>'javascript',
                          'rootWebPath'=>getcwd().'/src/TlAssetsBundle/Tests/web/',
                          'filters'=>array()
                    )
                ),
                array(
                    array('/bundles/test/less/'),
                    array('filters'=>array('less')),
                    'style',
                    array('name'=>'feaea60',
                          'files'=>array(array('src'=>getcwd().'/src/TlAssetsBundle/Tests/web/bundles/test/less/style.less',
                                               'dest'=>'/public/css/feaea60_part1_style.css')),
                          'type'=>'stylesheet',
                          'rootWebPath'=>getcwd().'/src/TlAssetsBundle/Tests/web/',
                          'filters'=>array('less')
                    )
                ),
                array(
                    array('/bundles/test/less/'),
                    array('filters'=>array('less','concat')),
                    'style',
                    array('name'=>'311b5b6',
                        'files'=>array(array('src'=>getcwd().'/src/TlAssetsBundle/Tests/web/bundles/test/less/style.less',
                                             'dest'=>'/public/css/311b5b6_part1_style.css')),
                        'type'=>'stylesheet',
                        'concatDest'=>'/public/css/311b5b6.css',
                        'rootWebPath'=>getcwd().'/src/TlAssetsBundle/Tests/web/',
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
        $tlAssetsManager = new TlAssetsManager($this->config);
        $tlAssetsManager->load($inputs, $attributes,$tag) ;

        $this->assertFileExists($this->config['buffer_folder'].'/'.$expected['name'].'.json');
        $actual = file_get_contents($this->config['buffer_folder'].'/'.$expected['name'].'.json');
        $actualData = json_decode($actual,true);

        $this->assertEquals($expected, $actualData);
    }

    public function tearDown()
    {
        if(file_exists(getcwd().'/src/TlAssetsBundle/Tests/tmp/')) {
            $this->_remove(getcwd().'/src/TlAssetsBundle/Tests/tmp/');
        }
    }
}