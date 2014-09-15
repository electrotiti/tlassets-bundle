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
use Symfony\Component\Finder\Finder;

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
        $twig = $this->getContainer()->get('twig');

        $kernel = $this->getContainer()->get('kernel');
        $path = $kernel->locateResource('@TlmoneyMainBundle');
        $finder = new Finder();
        $files = $finder->files()->in($path)->name('*.twig');

        foreach($files as $file) {
            $stream = $twig->tokenize($file->getContents());
            $twig->parse($stream);
        }
    }

} 