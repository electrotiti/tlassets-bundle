<?php
/**
 * User: Thierry LESZKOWICZ
 * Date: 11/08/14
 */

namespace TlAssetsBundle\Extension\Twig;

use Symfony\Component\Finder\Finder;
use TlAssetsBundle\Asset\TlAssetsCollection;
use Symfony\Component\Process\Process;
use TlAssetsBundle\Compiler\CompilerManager;

class TlAssetsManager
{

    /**
     * Folder to savefile buffer
     */
    const BUFFER_FOLDER = '/tlassets/buffer/';

    /**
     * Web folder
     */
    const WEB_FOLDER = '/web';

    private $config;
    private $debug;
    private $useCache;
    private $liveCompilation;

    private $webPath;
    private $bufferFolder;

    private $defaultFilters = array();
    private $variables  = array();
    private $compilerManager;

    private $buffer = false;
    private $bufferFileName;
    private $collection;

    /**
     * @param string $rootDir Root dir of project (remove /app if this )
     * @param string $rootCacheDir Chache dir
     * @param boolean $debug Enable or disable debug mode
     * @param boolean $useCache Use buffer file to return assets list to Twig
     * @param boolean $liveCompilation Compiles assets each page load
     * @param array $variables Variable to replace in assets path
     */
    public function __construct($config, $debug = false, $useCache = false, $liveCompilation = false)
    {
        $this->config = $config;
        $this->debug   = $debug;
        $this->useCache = $useCache;
        $this->liveCompilation = $liveCompilation;

        $this->webPath = $this->config['web_folder'].(substr($this->config['web_folder'],-1) != '/' ? '/' : '');
        $this->bufferFolder = $this->config['buffer_folder'].(substr($this->config['buffer_folder'],-1) != '/' ? '/' : '');
    }

    /**
     * @param array $defaultFilters
     */
    public function setDefaultFilters($defaultFilters)
    {
        $this->defaultFilters = $defaultFilters;
    }

    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    /**
     * @param CompilerManager $compilerManager
     */
    public function setCompilerManager(CompilerManager $compilerManager)
    {
        $this->compilerManager = $compilerManager;
    }

    /**
     * Fetch files and create buffer
     * @param array $inputs
     * @param array $attributes
     * @param string $tag
     * @throws \Exception
     */
    public function load($inputs, $attributes, $tag)
    {
        $attributes['filters'] = array_merge($this->defaultFilters, $attributes['filters']);
        $this->collection = new TlAssetsCollection($inputs, $attributes, $tag);
        $this->bufferFileName = $this->collection->getName().'.json';

        // Create or load buffer
        $this->_loadBuffer($inputs);

        // Compile assets if live compilation is enable
        if($this->liveCompilation) {
            $this->compilerManager->compileAssets($this->bufferFileName, false);
        }
    }

    /**
     * Get list of assets to return to the view
     * @return array
     * @throws \Exception
     */
    public function getAssetsPath()
    {
        if($this->buffer == false) {
            throw new \Exception('No buffer file. Please init manager with load() function before.');
        }

        $assetsPath = array();
        foreach($this->buffer['files'] as $file) {
            $assetsPath[] =  $file['dest'];
        }

        return $assetsPath;
    }

    private function _loadBuffer($inputs)
    {
        $this->buffer = false;

        // Use cache if enable
        if($this->useCache) {
            $bufferFile = $this->bufferFolder.$this->collection->getName().'.json';

            if(file_exists($bufferFile)) {
                $this->buffer = @file_get_contents($bufferFile);
                if($this->buffer === false) {
                    throw new \Exception('Unable to get content of buffer file : '.$bufferFile);
                }
            }
        }

        // Generate buffer file
        if($this->buffer === false) {

            $sourceFiles = $this->_getSourceFiles($inputs);
            foreach($sourceFiles as $filePath) {
                $this->collection->createAssets($filePath);
            }

            $this->buffer = $this->collection->exportBufferData($this->webPath);
        }

        // Save buffer file in cache folder
        $this->_saveBuffer($this->buffer);
    }

    /**
     * Save buffer file in cache folder
     * @throws \Exception
     */
    private function _saveBuffer()
    {
        // Create folder if it doesn't exist
        if(!file_exists($this->bufferFolder)) {
            mkdir($this->bufferFolder,0770, true);
        }

        // Write buffer file
        $bufferFile = $this->bufferFolder.$this->bufferFileName;
        if(false === file_put_contents($bufferFile,json_encode($this->buffer))) {
            throw new \Exception('Unable to write buffer file : '.$bufferFile);
        }
    }

    private function _getSourceFiles($inputs)
    {
        $sourceFiles = array();

        foreach($inputs as $input) {
            if("@" == $input[0]) {
                $path = $this->webPath.$this->_getWebPathByReference($input);
            } else {
                $path = $this->webPath.("/" == $input[0] ? substr($input,1,strlen($input) - 1) : $input);
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
                $files = $this->_find($path, $filter);
                foreach($files as $realPath) {
                    $sourceFiles[] = $realPath;
                }
            } else {
                if(false === file_exists($path)) {
                    throw new \Exception('Unable to find file : '.$path);
                }
                $sourceFiles[] = $path;
            }
        }

        return $sourceFiles;
    }

    /**
     * Read on FileSystem to get list of source file
     * @param $folder
     * @param bool $filter
     * @return array
     */
    private function _find($folder, $filter = false)
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

    /**
     * Replace variables in asset path
     * @param $path
     * @return mixed
     */
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