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
    const WEB_FOLDER = '/web';

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
        
        $this->webPath = $this->rootDir.self::WEB_FOLDER;
        $this->bufferFolder = $this->rootCacheDir.self::BUFFER_FOLDER;
        
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

        foreach($inputs as $input) {
            if("@" == $input[0]) {
                $path = $this->webPath.$this->_getWebPathByReference($input);
            } else {
                $path = $this->webPath.("/" != $input[0] ? '/'  :'').$input;
            }

            // Remove "*" if it's the last caractere
            if(substr($path,-1) == '*') {
                $path = substr($path,0, strlen($path)-1);
            }

            // Separate filter and path if path have something like this "/my/path/*.xx" where "xx" is a file extension
            $filter = false;
            $tmp = explode('*',$path);
            if(count($tmp) == 2) {
                $filter = '*'.$tmp[1];
                $path = $tmp[0];
            }

            // Replace variables in path
            $path = $this->_replaceVariables($path);

            if(is_dir($path)) {
                $files = $this->_getFiles($path, $filter);
                foreach($files as $realPath) {
                    $this->collection->createAssets($realPath);
                }
            } else {
                if(false === file_exists($this->webPath.$input)) {
                    throw new \Exception('Unable to find file : '.$this->webPath.$input);
                }
                $this->collection->createAssets($this->webPath.$input);
            }
        }

        // Save buffer file in cache
        $this->saveBuffer();

        // Compile assets if live compilation is enable
        if($this->liveCompilation) {
            $this->compilerManager->compileAssets($this->collection->getName().'.json');
        }
    }

    public function getTargetPath()
    {
        $targetPath = array();

        if($this->useCache) {
            $collectionName = $this->collection->getName();
            $bufferFile = $this->bufferFolder.$collectionName.'.json';

            if(!file_exists($bufferFile)) {
                $export = $this->collection->exportBufferData();
            } else {
                $content = @file_get_contents($bufferFile);
                if($content !== false) {
                    $export = json_decode($content,true);
                } else {
                    throw new \Exception('Unable to get content of buffer file : '.$bufferFile);
                }
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
        if(!file_exists($this->bufferFolder)) {
            mkdir($this->bufferFolder,0770, true);
        }

        $export = $this->collection->exportBufferData($this->webPath);
        //echo "<pre>".print_r($export,true)."</pre>";
        $bufferFile = $this->bufferFolder.$this->collection->getName().'.json';
        if(false === file_put_contents($bufferFile,json_encode($export))) {
            throw new \Exception('Unable to write buffer file : '.$bufferFile);
        }
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

    private function _replaceVariables($path)
    {
        foreach($this->variables as $key=>$var)
        {
            $path = str_replace('{'.$key.'}',$var, $path);
        }

        return $path;
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

        return $path;
    }
}