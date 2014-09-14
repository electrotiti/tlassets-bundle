<?php
/**
 * Created by PhpStorm.
 * User: thierry
 * Date: 14/08/14
 * Time: 17:39
 */

namespace TlAssetsBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TlAssetsBundle\Extension\Twig\TlAssetsExtension;
use TlAssetsBundle\Extension\Twig\TlAssetsManager;
use TlAssetsBundle\Extension\Twig\TlAssetsTokenParser;

class DumpAssetsCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('tlassets:dump')
            ->setDescription('Dump all assets');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loader = new \Twig_Loader_Filesystem('/home/electro/www/tlmoney/src/Tlmoney/MainBundle/Resources/views/Default');
        $twig = new \Twig_Environment($loader);

        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');
        $cacheDir = $this->getContainer()->getParameter('kernel.cache_dir');


        $manager = new TlAssetsManager($rootDir,$cacheDir,true,false);
        $manager->setDefaultFilters(array('less'));
        $tlAssetsExtension = new TlAssetsExtension($manager);

        $twig->addExtension($tlAssetsExtension);
        //$twig->render('index.html.twig');
        $stream = $twig->tokenize('index.html.twig','index.html.twig');

        $node = $stream->look();
        var_dump($node);die;

        $tokenParser = new TlAssetsTokenParser($manager,'');
        $tokenParser->parse($node);


//        $nodes = $twig->parse($stream);

//        $php = $twig->compile($nodes);
//        var_dump($php);
    }

} 