<?php

namespace TlAssetsBundle\Compiler;

use Symfony\Component\Finder\Finder;

class CompilerManager
{

    private $kernel;
    private $environment;
    private $rootDir;
    private $cacheDir;
    private $debug;
    private $bundles;

    public function __construct($kernel, \Twig_Environment $environment, $rootDir, $cacheDir, $debug, $bundles)
    {
        $this->kernel = $kernel;
        $this->environment = $environment;
        $this->rootDir = $rootDir;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
        $this->bundles = $bundles;
    }

    public function dumpBuffer()
    {
        $finder = new Finder();

        foreach($this->bundles as $bundle) {
            $pathToBundle = $this->kernel->locateResource('@'.$bundle);
            $files = $finder->files()->in($pathToBundle)->name('*.twig');

            foreach($files as $file) {
                $stream = $this->environment->tokenize($file->getContents());
                $this->environment->parse($stream);
            }
        }
    }

    public function compileAssets($output)
    {
        $gulpFolder = $this->rootDir.'vendor/node_modules/tlassets-bundle/';

        $process = new Process('cd '.$gulpFolder.' &&  ./node_modules/gulp/bin/gulp.js --buffer='.$this->cacheDir.'/tlassets/buffer/');
        $process->run(function ($type, $buffer) use ($output) {
                $output->writeln($buffer);
            });
    }

}