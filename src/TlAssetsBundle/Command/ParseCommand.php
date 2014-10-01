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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ParseCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('tlassets:parse')
            ->setDescription('Parse twig template to create buffer file of assets for Gulp')
            ->addOption('nodebug', null, InputOption::VALUE_NONE, 'Do not show any log');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hideLog = $input->getOption('nodebug');
        $twigEnv = $this->getContainer()->get('twig');

        $bundles = $this->getContainer()->getParameter('tl_assets.bundles');
        $finder = new Finder();

        foreach($bundles as $bundle) {

            if(!$hideLog) {
                $output->writeln("<info>[$bundle]</info>");
            }

            $pathToBundle = $this->getContainer()->get('kernel')->locateResource('@'.$bundle);
            $files = $finder->files()->in($pathToBundle)->name('*.twig');

            foreach($files as $file) {
                $stream = $twigEnv->tokenize($file->getContents());
                $twigEnv->parse($stream);
                if(!$hideLog) {
                    $output->writeln($file->getRealpath());
                }
            }
        }
    }
}