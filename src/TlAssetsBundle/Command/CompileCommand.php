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
        $compilerManager = $this->getContainer()->get('tl_assets.compiler');

        $callback = function ($type, $buffer) use($output) {
            if (Process::ERR === $type) {
                $output->writeln('<error>'.$buffer.'</error>');
            } else {
                $output->writeln('<info>'.$buffer.'</info>');
            }
        };

        $compilerManager->compileAssets(null, $callback);
    }

}