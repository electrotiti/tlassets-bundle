<?php
/**
 * Created by PhpStorm.
 * User: thierry
 * Date: 13/08/14
 * Time: 10:15
 */

namespace TlAssetsBundle\Asset;


use Symfony\Component\Finder\Finder;
use TlAssetsBundle\Extension\Twig\TlAssetsExtension;

class TlAssetsCollection
{
    const JS_FOLDER = '/js';
    const CSS_FOLDER = '/css';

    private $inputs;
    private $attributes;
    private $tag;
    private $assets = array();
    private $hash = null;
    private $buffer = false;

    public function __construct($inputs, $attributes, $tag)
    {
        $this->inputs       = $inputs;
        $this->attributes   = $attributes;
        $this->tag = $tag;

        if(!in_array($this->tag,array(TlAssetsExtension::TAG_STYLESHEET, TlAssetsExtension::TAG_JAVASCRIPT))) {
            throw new \Exception('Unknow tag '.$this->tag);
        }

        $this->filters = array_key_exists('filters',$this->attributes) ? $this->attributes['filters'] : array();
        $this->generateHash = in_array('hash',$this->filters);
    }

    public function getAssets()
    {
        return $this->assets;
    }

    public function addAssets($asset)
    {
        $this->assets[$asset->getFilename()] = $asset;
        return $this;
    }

    public function createAssets($realPath)
    {
        $namePrefix = $this->getName().'_part'.(count($this->assets) + 1).'_';
        $asset = new TlAssetsFile($realPath, $namePrefix, $this->_getExtension(),$this->generateHash);
        $this->addAssets($asset);
    }

    public function getHash()
    {
        if(false ==  $this->hash) {

            $filesHash = array();
            foreach($this->assets as $asset) {
                $asset->load();
                $filesHash[] = $asset->getHash();
            }

            $filesHash = implode(',',$filesHash);
            $filesHash = sha1($filesHash);
            $this->hash = substr($filesHash,0,7);
        }

        return $this->hash;
    }

    public function getName()
    {
        return substr(sha1(serialize($this->inputs).serialize($this->attributes)), 0, 7);
    }

    public function exportBufferData($rootWebPath, $destFolder)
    {
        if($this->buffer == false) {
            $tagType = array(TlAssetsExtension::TAG_STYLESHEET=>'stylesheet',TlAssetsExtension::TAG_JAVASCRIPT=>'javascript');

            $files = array();
            foreach($this->assets as $asset) {
                $files[] = array('src'=>$asset->getRealFilePath(), 'dest'=>$this->_getOutput($destFolder).$asset->getFilename());
            }

            $attributes = array('name'=>$this->getName(),
                                'files'=>$files,
                                'type'=>$tagType[$this->tag],
                                'rootWebPath'=>$rootWebPath);

            if(in_array('concat',$this->filters)) {
                $contactDest = $this->_getOutput($destFolder).$this->getName().($this->generateHash ? '_'.$this->getHash() : '').'.'.$this->_getExtension();
                $attributes['concatDest'] = $contactDest;
            }

            $export = array_merge($attributes, $this->attributes);
            $this->buffer = $export;
        }
        return $this->buffer;
    }

    public function getAssetsPathList()
    {
        $assetsPath = array();

        if(in_array('concat',$this->filters)) {
            $assetsPath[] = $this->buffer['concatDest'];
        } else {
            foreach($this->buffer['files'] as $file) {
                $assetsPath[] =  $file['dest'];
            }
        }

        return $assetsPath;
    }


    private function _getExtension()
    {
        switch($this->tag) {
            case TlAssetsExtension::TAG_STYLESHEET :
                $extension = 'css';
                break;

            case TlAssetsExtension::TAG_JAVASCRIPT :
                $extension = 'js';
                break;

            default:
                throw new \Exception('Unknow tag '.$this->tag);
            break;
        }

        if(in_array('minify',$this->filters)) {
            $extension = 'min.'.$extension;
        }

        return $extension;
    }


    private function _getOutput($destFolder)
    {
        if(array_key_exists('output',$this->attributes)) {
            $output = substr($this->attributes['output'],-1) !== "/" ? $this->attributes['output'].'/' : $this->attributes['output'];
        } else {

            $subFolders = array(TlAssetsExtension::TAG_STYLESHEET=>self::CSS_FOLDER,
                                TlAssetsExtension::TAG_JAVASCRIPT=>self::JS_FOLDER);
            $output = $destFolder.$subFolders[$this->tag].'/';
        }

        return $output;
    }
}