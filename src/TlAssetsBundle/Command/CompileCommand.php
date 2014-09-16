<?php

namespace TlAssetsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

class CompileCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('tlassets:compile')
            ->setDescription('Compile assets based on Gulp buffer');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $root = $this->getContainer()->getParameter('kernel.root_dir').'/../';
        $cacheDir = $this->getContainer()->getParameter('kernel.cache_dir');

        $gulpFolder = $root.'vendor/node_modules/tlassets-bundle/';

        $process = new Process('cd '.$gulpFolder.' &&  ./node_modules/gulp/bin/gulp.js --buffer='.$cacheDir.'/tlassets/buffer/');
        $process->run(function ($type, $buffer) use ($output) {
                $output->writeln($buffer);
            });
    }

}