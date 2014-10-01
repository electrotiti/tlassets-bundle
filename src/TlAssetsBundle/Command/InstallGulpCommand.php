<?php

namespace TlAssetsBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class InstallGulpCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('tlassets:install:gulp')
             ->setDescription('Install Gulp and his dependencies')
             ->addOption('nodebug', null, InputOption::VALUE_NONE, 'Do not show any log');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hideLog = $input->getOption('nodebug');
        $rootDir = $this->getContainer()->get('kernel')->getRootDir()."/../";
        $gulpSrcFolder = $rootDir.'vendor/electrotiti/tlassets-bundle/src/TlAssetsBundle/Compiler/Gulp/';
        $config = $this->getContainer()->getParameter('tl_assets.config');

        $return = $this->_installGulp($rootDir, $gulpSrcFolder, $config['node_folder'],$hideLog);

        if($return == 0) {
            if(!$hideLog) {
                $output->writeln('<info>Gulp and dependencies successfully installed.</info>');
            }
        } else {
            $output->writeln('<error>Some errors occurred during installation, please check logs.</error>');
        }
    }

    private function _installGulp($rootDir, $gulpSrcFolder, $destNodeFolder, $hideLog = false)
    {
        // Build the command line
        $command = 'cd '.$rootDir.' && npm install '.$gulpSrcFolder.' --prefix '.$destNodeFolder;

        if($hideLog) {
            $command .= ' 2>&1';
        }

        // Command execution
        exec ($command,$result,$return);

        return $return;
    }

}