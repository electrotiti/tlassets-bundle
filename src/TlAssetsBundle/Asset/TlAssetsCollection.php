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
    private $inputs;
    private $attributes;
    private $tag;
    private $assets = array();
    private $hash = null;

    public function __construct($inputs, $attributes, $tag)
    {
        $this->inputs       = $inputs;
        $this->attributes   = $attributes;
        $this->tag = $tag;

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

    public function exportBufferData($rootWebPath = '')
    {
        $files = array();
        foreach($this->assets as $asset) {
            $files[] = array('src'=>$asset->getRealFilePath(), 'dest'=>$this->_getOutput().$asset->getFilename());
        }

        $contactDest = $this->_getOutput().$this->getName().($this->generateHash ? '_'.$this->getHash() : '').'.'.$this->_getExtension();

        switch($this->tag) {
            case TlAssetsExtension::TAG_STYLESHEET :
                $type = 'stylesheet';
                break;

            case TlAssetsExtension::TAG_JAVASCRIPT :
                $type = 'javascript';
                break;
        }

        $export = array_merge(array('name'=>$this->getName(),
                                    'files'=>$files,
                                    'concatDest'=>$contactDest,
                                    'type'=>$type,
                                    'rootWebPath'=>$rootWebPath), $this->attributes);

        return $export;
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
                $extension = '';
                break;
        }

        if(in_array('minify',$this->filters)) {
            $extension = 'min.'.$extension;
        }

        return $extension;
    }


    private function _getOutput()
    {
        if(array_key_exists('output',$this->attributes)) {
            $output = substr($this->attributes['output'],-1) !== "/" ? $this->attributes['output'].'/' : $this->attributes['output'];
        } else {

            switch($this->tag) {
                case TlAssetsExtension::TAG_STYLESHEET :
                    $subFolder = 'css';
                    break;

                case TlAssetsExtension::TAG_JAVASCRIPT :
                    $subFolder = 'js';
                    break;

                default:
                    throw new \Exception('Unknow tag '.$this->tag);
                break;
            }
            $output = '/public/'.$subFolder.'/';
        }

        return $output;
    }
}