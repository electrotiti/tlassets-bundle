<?php
/**
 * User: Thierry LESZKOWICZ
 * Date: 11/08/14
 */

namespace TlAssetsBundle\Extension\Twig;

use Symfony\Component\Finder\Finder;
use TlAssetsBundle\Asset\TlAssetsCollection;

class TlAssetsManager
{

    private $rootDir;
    private $debug;
    private $collection;
    private $defaultFilters;
    private $rootCacheDir;

    public function __construct($rootDir, $rootCacheDir, $debug)
    {
        $this->rootDir = str_replace('/app','',$rootDir);
        $this->debug   = $debug;
        $this->rootCacheDir = $rootCacheDir;
    }

    public function setDefaultFilters($defaultFilters)
    {
        $this->defaultFilters = $defaultFilters;
    }


    public function load($inputs, $attributes, $tag)
    {
        $attributes['filters'] = array_merge($this->defaultFilters, $attributes['filters']);

        $this->collection = new TlAssetsCollection($inputs, $attributes, $tag);

        $webPath = $this->_getWebPath();
        foreach($inputs as $input) {
            if("@" == $input[0]) {
                $path = $webPath.$this->_getWebPathByReference($input);
            } else {
                $path = $webPath.$input;
            }

            // TODO Correct error if path ending by "*.xx"
            if(is_dir($path)) {
                foreach($this->_getFiles($path) as $realPath) {
                    $this->collection->createAssets($realPath);
                }
            } else {
                if(false === file_exists($webPath.$input)) {
                    throw new \Exception('Unable to find file : '.$webPath.$input);
                }
                $this->collection->createAssets($webPath.$input);
            }
        }

        $this->saveBuffer();
    }

    public function getTargetPath()
    {
        $targetPath = array();
        $export = $this->collection->exportBufferData();

        foreach($export['files'] as $file) {
            $targetPath[] =  $file['dest'];
        }

        return $targetPath;
    }

    public function saveBuffer()
    {
        $dir = $this->rootCacheDir.'/tlassets/';
        if(!file_exists($dir)) {
            mkdir($dir,0770, true);
        }

        $export = $this->collection->exportBufferData($this->_getWebPath());
        echo "<pre>".print_r($export,true)."</pre>";
        $bufferFile = $dir.$this->collection->getName().'.json';
        file_put_contents($bufferFile,json_encode($export));
    }

    private function _getFiles($folder)
    {
        $filter = false;

        $tmp = explode('*',$folder);
        if(count($tmp) == 2) {
            $filter = '*'.$tmp[1];
            $folder = $tmp[0];
        }

        $finder = new Finder();
        $finder->files()->in($folder);
        if(false !== $filter) {
            $finder->name($filter);
        }

        foreach($finder as $file) {
            $files[] = $file->getRealpath();
        }

        return $files;
    }

    private function _getWebPath()
    {
        return $this->rootDir.'/web';
    }

    /**
     * Transform a reference like "@MyAwesomeBundle/Resources/public/css/" to web path like "/bundles/awesomebundle/css/"
     * @param $reference
     * @return Path
     */
    private function _getWebPathByReference($reference)
    {
        $reference = ltrim ($reference,'@');
        $reference = str_replace('Bundle','',$reference);
        $tmp = explode('/',$reference);
        foreach($tmp as $k=>$t) {
            if('public' === $t || 'Resources' === $t) {
                unset($tmp[$k]);
            }
        }
        $tmp = array_map("strtolower",$tmp);
        return '/bundles/'.implode('/',$tmp);
    }

}