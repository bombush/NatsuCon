<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Custom\Content;
use Nette\Utils\ArrayHash;

/**
 * Description of ImageAttachment
 *
 * @author Josef Hruby
 */
class ImageAttachment {

    //put your code here
    protected $_row;

    const THUMB_SMALL = "200x200";
    const THUMB_LIST = "400x400";
    const FULL = '650x650';

    const IMAGE_DIR = 'images/uploaded/attachment/';
    const THUMB_DIR = 'thumbs/';

    public $smallFile;
    public $listFile;
    public $originalFile;
    private $basePath;

    public function setRow(ArrayHash $row) {
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
        $finalFilename = $this->generateFinalFilename();
        $this->saveOriginal();
        $this->saveThumbs();

        return $finalFilename;
    }
    
    private function saveOriginal(){
        $filename = $this->generateFinalFilename();
        $this->_row->file->move(self::IMAGE_DIR.$filename);
        
    }
    
    public function saveThumbs(){
        $filename = $this->generateFinalFilename();
        $originalFile = self::IMAGE_DIR.$filename;
        $this->createThumb($originalFile);
        
    }

    protected function generateFinalFilename() {
        return $this->_row->contentId . "-" . $this->_row->file->getSanitizedName();
    }
    
    public function createThumb($filename){
        
        $image =  \Nette\Utils\Image::fromFile($filename);
        $this->thumbResize($image, self::THUMB_LIST);
        $this->thumbResize($image, self::THUMB_SMALL);
       // $this->thumbResize($image, self::THUMB_PRG);
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
        $image->resize($d[0],$d[1])->sharpen()->save(self::IMAGE_DIR.self::THUMB_DIR.$thumbFileName);
        //echo $fileName; exit;
        
    }

    private function thumbFile($thumbType, $filePath) {
        if(strstr($filePath, ".jpg")){
           return str_replace(".jpg", "-" . $thumbType . ".jpg", $filePath);
        }else if(strstr($filePath, ".png")){
           return str_replace(".png", "-" . $thumbType . ".png", $filePath); 
        }
        
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
