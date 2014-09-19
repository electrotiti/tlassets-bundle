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

class DumpCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('tlassets:dump')
            ->setDescription('Dump buffer of assets for Gulp');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $twigEnv = $this->getContainer()->get('twig');

        $bundles = $this->getContainer()->getParameter('tl_assets.bundles');
        $finder = new Finder();

        foreach($bundles as $bundle) {
            $pathToBundle = $this->getContainer()->get('kernel')->locateResource('@'.$bundle);
            $files = $finder->files()->in($pathToBundle)->name('*.twig');

            foreach($files as $file) {
                $stream = $twigEnv->tokenize($file->getContents());
                $twigEnv->parse($stream);
            }
        }
    }
}