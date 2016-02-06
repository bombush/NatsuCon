<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Custom\Content;

/**
 * Description of ImageAttachment
 *
 * @author Josef Hruby
 */
class ImageAttachment {

    //put your code here
    private $_row;

    const THUMB_SMALL = "100x100";
    const THUMB_LIST = "200x200";
    const IMAGE_DIR = 'images/uploaded/attachment/';
    const THUMB_DIR = 'thumbs/';

    public $smallFile;
    public $listFile;
    public $originalFile;
    private $basePath;

    public function setRow($row) {
        $this->_row = $row;
    }

    public function run() {
        $this->basePath = "./";

        $this->file("originalFile", $this->basePath . self::IMAGE_DIR . $this->_row->url);
        $this->file("smallFile", $this->thumbFile(self::THUMB_SMALL, $this->basePath . self::IMAGE_DIR . self::THUMB_DIR . $this->_row->url));
        $this->file("listFile", $this->thumbFile(self::THUMB_LIST, $this->basePath . self::IMAGE_DIR . self::THUMB_DIR . $this->_row->url));

        //  print_r($this);
    }
    
    public function create(){
        $this->saveOriginal();
        $this->saveThumbs();
       // print_r($this->_row); exit;
    }
    
    private function saveOriginal(){
        $this->_row->file->move(self::IMAGE_DIR.$this->_row->contentId."-".$this->_row->file->getSanitizedName());
        
    }
    
    public function saveThumbs(){
        $originalFile = self::IMAGE_DIR.$this->_row->contentId."-".$this->_row->file->getSanitizedName();
        $this->createThumb($originalFile);
        
    }
    
    public function createThumb($filename){
        
        $image =  \Nette\Utils\Image::fromFile($filename);
        $this->thumbResize($image, self::THUMB_LIST);
        $this->thumbResize($image, self::THUMB_SMALL);
     //   exit;
        
    }
    
    public function thumbFilename($dim){
        $fileNamePos = strrpos($this->_row->file->getSanitizedName(), ".");
        $fileName = substr($this->_row->file->getSanitizedName(),0,$fileNamePos);
        $fileExt =   substr($this->_row->file->getSanitizedName(),$fileNamePos + 1);
        return $this->_row->contentId."-".$fileName."-".$dim.".".$fileExt;
        
    }
    
    /**
     * 
     * @param \Nette\Utils\Image $image
     * @param [] $dims
     */
    public function thumbResize($image, $dim){
        
        $thumbFileName = $this->thumbFilename($dim);
        $d = explode("x", $dim);
        $image->resize($d[0], $d[1])->sharpen()->save(self::IMAGE_DIR.self::THUMB_DIR.$thumbFileName);
        //echo $fileName; exit;
        
    }

    private function thumbFile($thumbType, $filePath) {
        return str_replace(".jpg", "-" . $thumbType . ".jpg", $filePath);
    }

    private function file($fileType, $filePath) {
        //echo ">>".$filePath."<br>";
        if (file_exists($filePath)) {
            // echo "ok";
            $this->{$fileType} = str_replace($this->basePath, "/", $filePath);
        } else {
            // echo "ko";
            $this->{$fileType} = NULL;
        }
    }

}
