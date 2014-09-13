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

class DumpAssetsCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('tlassets:dump')
            ->setDescription('Dump assets');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../../MainBundle/Resources/views/Default/');
        $twig = new \Twig_Environment($loader);
        $twig->render('index.html.twig');
        $stream = $twig->tokenize('index.html.twig','index.html.twig');
        $nodes = $twig->parse($stream);
        $php = $twig->compile($nodes);
        var_dump($php);
    }

} 