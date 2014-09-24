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
} 