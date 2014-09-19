<?php
/**
 * User: Thierry LESZKOWICZ
 * Date: 11/08/14
 */

namespace TlAssetsBundle\Extension\Twig;

use Symfony\Component\Finder\Finder;
use TlAssetsBundle\Asset\TlAssetsCollection;
use Symfony\Component\Process\Process;

class TlAssetsManager
{

    const BUFFER_FOLDER = '/tlassets/buffer/';

    private $rootDir;
    private $debug;
    private $collection;
    private $defaultFilters;
    private $rootCacheDir;
    private $liveCompilation;
    private $compilerManager;
    private $variables;

    public function __construct($rootDir, $rootCacheDir, $debug, $useCache, $liveCompilation, $variables)
    {
        $this->rootDir = str_replace('/app','',$rootDir);
        $this->rootCacheDir = $rootCacheDir;
        $this->debug   = $debug;
        $this->useCache = $useCache;
        $this->liveCompilation = $liveCompilation;
        $this->variables = $variables;
    }

    public function setDefaultFilters($defaultFilters)
    {
        $this->defaultFilters = $defaultFilters;
    }

    public function setCompilerManager($compilerManager)
    {
        $this->compilerManager = $compilerManager;
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

            if(substr($path,-1) == '*') {
                $path = substr($path,0, strlen($path)-1);
            }

            $filter = false;
            $tmp = explode('*',$path);
            if(count($tmp) == 2) {
                $filter = '*'.$tmp[1];
                $path = $tmp[0];
            }

            if(is_dir($path)) {
                $files = $this->_getFiles($path, $filter);
                foreach($files as $realPath) {
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

        if($this->liveCompilation) {
            $this->compilerManager->compileAssets($this->collection->getName().'.json');
        }
    }

    public function getTargetPath()
    {
        $targetPath = array();

        if($this->useCache) {
            $bufferDir = $this->rootCacheDir.self::BUFFER_FOLDER;
            $collectionName = $this->collection->getName();

            if(!file_exists($bufferDir.$collectionName.'.json')) {
                $export = $this->collection->exportBufferData();
            } else {
                $export = json_decode(file_get_contents($bufferDir.$collectionName.'.json'),true);
            }
        } else {
            $export = $this->collection->exportBufferData();
        }

        foreach($export['files'] as $file) {
            $targetPath[] =  $file['dest'];
        }

        return $targetPath;
    }

    public function saveBuffer()
    {
        $dir = $this->rootCacheDir.self::BUFFER_FOLDER;
        if(!file_exists($dir)) {
            mkdir($dir,0770, true);
        }

        $export = $this->collection->exportBufferData($this->_getWebPath());
        //echo "<pre>".print_r($export,true)."</pre>";
        $bufferFile = $dir.$this->collection->getName().'.json';
        file_put_contents($bufferFile,json_encode($export));
    }

    private function _getFiles($folder, $filter = false)
    {

        $finder = new Finder();
        $finder->files()->in($folder)->depth('== 0');
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
        $path ='/bundles/'.implode('/',$tmp);

        foreach($this->variables as $key=>$var)
        {
            $path = str_replace('{'.$key.'}',$var, $path);
        }

        return $path;
    }

}