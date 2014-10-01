<?php

namespace TlAssetsBundle\Tests\Extension\Twig;

use TlAssetsBundle\Extension\Twig\TlAssetsManager;
use TlAssetsBundle\Tests\AbstractTest;

class TlAssetsManagerTest extends AbstractTest
{

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
                          'rootWebPath'=>'./src/TlAssetsBundle/Tests/web/',
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
                          'rootWebPath'=>'./src/TlAssetsBundle/Tests/web/',
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
                        'rootWebPath'=>'./src/TlAssetsBundle/Tests/web/',
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
        if(file_exists(self::TMP_FOLDER)) {
            $this->_remove(self::TMP_FOLDER);
        }
    }
}