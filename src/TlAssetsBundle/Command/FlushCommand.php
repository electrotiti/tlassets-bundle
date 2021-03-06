<?php

namespace TlAssetsBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class FlushCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tlassets:flush')
            ->setDescription('Remove buffer file and assets previously generated')
            ->addOption('nodebug', null, InputOption::VALUE_NONE, 'Do not show any log');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hideLog = $input->getOption('nodebug');
        $config = $this->getContainer()->getParameter('tl_assets.config');

        $flush = function($folder) use ($output,$hideLog)
        {
            if(false === file_exists($folder)) {
                if(!$hideLog) {
                    $output->writeln('<info>'.$folder.' doesn\'t exist.</info>');
                }
            } else {
                $res = $this->_remove($folder);

                if($res) {
                    if(!$hideLog) {
                        $output->writeln('<info>[FLUSHED] '.$folder.'</info>');
                    }
                } else {
                    $output->writeln('<error>[ERROR] '.$folder.'</error>');
                }
            }
        };

        $folders = array($config['buffer_folder'], $config['web_folder'].$config['public_folder']);

        foreach($folders as $folder) {
            $flush($folder);
        }

    }

    private function _remove($path)
    {
        if (is_dir($path) === true)
        {
            $files = array_diff(scandir($path), array('.', '..'));

            foreach ($files as $file)
            {
                $this->_remove(realpath($path) . '/' . $file);
            }

            return rmdir($path);
        }

        else if (is_file($path) === true)
        {
            return unlink($path);
        }

        return false;
    }
}