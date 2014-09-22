<?php

namespace TlAssetsBundle\Compiler;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

class CompilerManager
{

    private $kernel;
    private $rootDir;
    private $cacheDir;
    private $debug;
    private $logger;

    public function __construct($kernel, $rootDir, $cacheDir, $debug)
    {
        $this->kernel = $kernel;
        $this->rootDir = $rootDir;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function compileAssets($bufferFilename = null, $callback = null)
    {
        $gulpFolder = $this->rootDir.'/../vendor/node_modules/tlassets-bundle/';
        $buffer = $this->cacheDir.'/tlassets/buffer/'.($bufferFilename != null ? $bufferFilename : '');

        $command = 'cd '.$gulpFolder.' &&  ./node_modules/gulp/bin/gulp.js --buffer='.$buffer;
        if($this->debug) {
            $command .= ' --verbose';
        }

        $process = new Process($command);

        if($callback == null && $this->debug) {
            $logger = $this->logger;
            $callback = function($type, $output) use($logger){
                if(Process::ERR === $type) {
                    $logger->error('[Tlassets] '.$output);
                } else {
                    $logger->debug('[Tlassets] '.$output);
                }
            };
        }

        $process->run($callback);


        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }

}