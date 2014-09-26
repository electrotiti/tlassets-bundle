<?php

namespace TlAssetsBundle\Compiler;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use TlAssetsBundle\Extension\Twig\TlAssetsManager;

class CompilerManager
{
    private $rootDir;
    private $config;
    private $debug;
    private $logger;

    public function __construct($rootDir, $config, $debug)
    {
        $this->rootDir = $rootDir;
        $this->config = $config;
        $this->debug = $debug;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function compileAssets($bufferFilename = null, $showLog = true, $verbose = false)
    {
        $gulpFolder = $this->config['node_folder'].'/tlassets-bundle/';
        $buffer = $this->config['buffer_folder'].'/'.($bufferFilename != null ? $bufferFilename : '');

        $command = $gulpFolder."node_modules/gulp/bin/gulp.js --cwd=$gulpFolder --buffer=$buffer";
        if($verbose) {
            $command .= ' --verbose';
        }

        while (@ ob_end_flush()); // end all output buffers if any

        $proc = popen($command, 'r');
        while (!feof($proc))
        {
            if($this->debug) {
                $this->logger->debug('[Tlassets] '.fread($proc, 4096));
            }

            if($showLog) {
                echo fread($proc, 4096);
            }
            @ flush();
        }
        pclose($proc);
    }

}