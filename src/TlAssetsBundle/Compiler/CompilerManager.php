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

    public function __construct($kernel, $rootDir, $cacheDir, $debug)
    {
        $this->kernel = $kernel;
        $this->rootDir = $rootDir;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    public function compileAssets($bufferFile = null, $output = null)
    {
        $gulpFolder = $this->rootDir.'/../vendor/node_modules/tlassets-bundle/';
        $buffer = $this->cacheDir.'/tlassets/buffer/'.($bufferFile != null ? $bufferFile : '');
        $log = $this->cacheDir.'/tlassets/log/'.($bufferFile != null ? $bufferFile.'.log' : 'common.log');

        if(!file_exists($this->cacheDir.'/tlassets/log/')) {
            mkdir($this->cacheDir.'/tlassets/log/',0770, true);
        }

        $command = 'cd '.$gulpFolder.' &&  ./node_modules/gulp/bin/gulp.js --buffer='.$buffer.' >> '.$log;

        $process = new Process($command);

        if($output != null) {
            $process->run(function ($type, $buffer) use ($output) {
                    $output->writeln($buffer);
                });
        }else {
            $process->run();
        }

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }

}