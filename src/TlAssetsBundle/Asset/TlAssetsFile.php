<?php

namespace TlAssetsBundle\Asset;


class TlAssetsFile
{
    private $realFilePath;
    private $content = false;
    private $filename;

    public function __construct($realFilePath, $namePrefix, $extension, $nameWithHash = false)
    {
        $this->realFilePath = $realFilePath;

        if($nameWithHash) {
            $this->load();
        }

        $fileName = substr($this->realFilePath, 0, strpos($this->realFilePath, "."));
        $fileName = substr(strrchr($fileName, '/'),1);
        $this->filename = $namePrefix.$fileName.($nameWithHash ? '_'.$this->getHash() : '').'.'.$extension;
    }

    public function load()
    {
        if(false === $this->content) {
            $this->content = @file_get_contents($this->realFilePath);
            if(false === $this->content) {
                throw new \Exception('Unable to laod file : '.$this->realFilePath);
            }
        }
        return $this;
    }


    public function getHash()
    {
        if(false === $this->content) {
            $this->load();
        }
        return substr(sha1($this->content),0,7);
    }

    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return mixed
     */
    public function getRealFilePath()
    {
        return $this->realFilePath;
    }




//    private function _buildDestinationPath()
//    {
//        // Get filter list from attributes
//        $filters = array_key_exists('filter',$this->attributes) ? $this->attributes['filter'] : array();
//
//        // If is CSS compilation we will change some folder & $extension
//        $isCssCompilation = 0 !== count(array_intersect($this->cssPreprocessor, $filters));
//
//
//        // Build output folder
//        if(array_key_exists('output',$this->attributes)) {
//            $output = substr($this->attributes['output'],-1) !== "/" ? $this->attributes['output'].'/' : $this->attributes['output'];
//        } else {
//
//            if($isCssCompilation) {
//                $subFolder = 'css';
//            } else {
//                $tmp = explode('/',$this->realFilePath);
//                $subFolder = $tmp[count($tmp) - 2];
//            }
//
//            $output = '/public/'.$subFolder.'/';
//        }
//
//        // Build final file extension
//        $extension = $isCssCompilation ? 'css' : substr(strrchr($this->realFilePath, "."), 1);
//
//        // Add ".min", in filename if file will be minify
//        if(in_array('minify',$filters)) {
//            $extension = 'min.'.$extension;
//        }
//
//        // Build filename
//        $fileName = substr(strrchr($this->realFilePath, "/"), 1);
//        $fileName = str_replace('.'.$extension,'',$fileName);
//
//        // Return final destination of file
//        return $output.$this->collectionName.'_'.$fileName.'.'.$extension;
//    }


} 