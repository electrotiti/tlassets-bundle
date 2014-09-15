<?php

namespace TlAssetsBundle\Extension\Twig;

use Symfony\Component\Finder\Finder;

class TlAssetsDumper
{

    private $kernel;
    private $environment;
    private $bundles;

    public function __construct($kernel, \Twig_Environment $environment, $bundles)
    {
        $this->kernel = $kernel;
        $this->environment = $environment;
        $this->bundles = $bundles;
    }

    public function dumpBufferInCache()
    {
        $finder = new Finder();

        foreach($this->bundles as $bundle) {
            $pathToBundle = $this->kernel->locateResource($bundle);
            $files = $finder->files()->in($pathToBundle)->name('*.twig');

            foreach($files as $file) {
                $stream = $this->environment->tokenize($file->getContents());
                $this->environment->parse($stream);
            }
        }
    }

}