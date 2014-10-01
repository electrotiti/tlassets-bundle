<?php
/**
 * Created by PhpStorm.
 * User: thierry
 * Date: 14/08/14
 * Time: 17:39
 */

namespace TlAssetsBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class DumpCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('tlassets:dump')
            ->setDescription('Clean buffer, parse twig template and compile assets (tlassets:flush & tlassets:parse & tlassets:compile)')
            ->addOption('nodebug', null, InputOption::VALUE_NONE, 'Do not show any log');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hideLog = $input->getOption('nodebug');

        if(!$hideLog) {
            $output->writeln('<info>*** FLUSH ****</info>');
        }
        $command = $this->getApplication()->find('tlassets:flush');
        $returnCode = $command->run($input, $output);
        if($returnCode != 0) {
            $output->writeln('<error>Error of flushing</error>');
            exit(1);
        }

        if(!$hideLog) {
            $output->writeln("\n<info>*** PARSING ****</info>");
        }
        $command = $this->getApplication()->find('tlassets:parse');
        $returnCode = $command->run($input, $output);
        if($returnCode != 0) {
            $output->writeln('<error>Error of parsing</error>');
            exit(1);
        }

        if(!$hideLog) {
            $output->writeln("\n<info>*** COMPILATION ****</info>");
        }
        $command = $this->getApplication()->find('tlassets:compile');
        $returnCode = $command->run($input, $output);
        if($returnCode != 0) {
            $output->writeln('<error>Error of compilation</error>');
            exit(1);
        }

    }
}