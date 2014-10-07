<?php
namespace TlAssetsBundle\Tests\Extension\Twig;

use TlAssetsBundle\Extension\Twig\TlAssetsExtension;
use TlAssetsBundle\Extension\Twig\TlAssetsManager;
use TlAssetsBundle\Extension\Twig\TlAssetsTokenParser;
use TlAssetsBundle\Tests\AbstractTest;

class TlAssetsExtensionTest extends AbstractTest
{
    public function testExtension()
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../../Ressources/');
        $twig = new \Twig_Environment($loader);

        $tlAssetsManager = new TlAssetsManager($this->config);
        $twig->addExtension(new TlAssetsExtension($tlAssetsManager));
        $html = $twig->render('test.html.twig');

        //file_put_contents(__DIR__.'/../../Ressources/test-expected.html',$html);

        $htmlExpected = file_get_contents(__DIR__.'/../../Ressources/test-expected.html');
        $this->assertEquals($htmlExpected,$html);

    }
}