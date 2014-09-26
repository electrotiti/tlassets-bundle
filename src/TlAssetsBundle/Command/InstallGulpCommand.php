<?php

namespace TlAssetsBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class InstallGulpCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('tlassets:install:gulp')
             ->setDescription('Install Gulp and his dependencies');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $root = $this->getContainer()->get('kernel')->getRootDir()."/../";
        $gulpSRCFolder = $root.'vendor/electrotiti/tlassets-bundle/src/TlAssetsBundle/Compiler/Gulp/';
        $config = $this->getContainer()->getParameter('tl_assets.config');

        // Build the command line
        $command = 'cd '.$root.' && npm install '.$gulpSRCFolder.' --prefix '.$config['node_folder'];

        // Command execution
        exec ($command,$result,$return);

        if($return == 0) {
            $output->writeln('<info>Gulp and dependencies successfully installed.</info>');
        } else {
            $output->writeln('<error>Some errors occurred during installation, please check logs.</error>');
        }
    }

}