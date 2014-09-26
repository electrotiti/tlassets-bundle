<?php

namespace TlAssetsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class CompileCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('tlassets:compile')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Show more debug log info')
            ->addOption('nodebug', null, InputOption::VALUE_NONE, 'Do not show any log')
            ->setDescription('Compile assets based on Gulp buffer created by the command tlassets:dump');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $verbose = $input->getOption('debug');
        $showLog = $input->getOption('nodebug');
        $compilerManager = $this->getContainer()->get('tl_assets.compiler');
        $compilerManager->compileAssets(null,!$showLog, $verbose);
    }

}