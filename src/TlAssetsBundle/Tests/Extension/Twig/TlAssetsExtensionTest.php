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
        $twig->render('test.html.twig');

        $this->assertFileExists(__DIR__.'/../../tmp/cache/041df95.json');
        $this->assertFileExists(__DIR__.'/../../tmp/cache/527b1cf.json');

    }

}