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
        $this
            ->setName('tlassets:install:gulp')
            ->setDescription('Install Gulp and his dependencies');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $root = $this->getContainer()->get('kernel')->getRootDir()."/../";
        $gulpFolder = $root.'vendor/electrotiti/tlassets-bundle/src/TlAssetsBundle/Compiler/Gulp/';

        $process = new Process('cd '.$root.' && npm install '.$gulpFolder.' --prefix ./vendor/node_modules');
        $process->run(function ($type, $buffer) use ($output) {
            $output->writeln($buffer);
        });
    }

}