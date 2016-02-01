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
    const THUMB_LIST= "200x200";
    const IMAGE_DIR = 'data/uploaded/attachment/';
    const THUMB_DIR = 'thumbs/';
    public $smallFile;
    public $listFile;
    public $originalFile;
    private $basePath;
    
    
    public function setRow($row){
        $this->_row = $row;
    }
    
    public function run(){
       // echo __DIR__;
        $this->basePath = "./";
      //  $files = scandir("./");
      //  print_r($files);
        $this->file("originalFile", $this->basePath.self::IMAGE_DIR.$this->_row->url);
        $this->file("smallFile",$this->thumbFile(self::THUMB_SMALL, $this->basePath.self::IMAGE_DIR.self::THUMB_DIR.$this->_row->url));
        $this->file("listFile",$this->thumbFile(self::THUMB_LIST, $this->basePath.self::IMAGE_DIR.self::THUMB_DIR.$this->_row->url));
        
      //  print_r($this);
    }
    
    private function thumbFile($thumbType,$filePath){
        return str_replace(".jpg", "-".$thumbType.".jpg", $filePath);
    }
    
private function file($fileType,$filePath){
    //echo ">>".$filePath."<br>";
    if(file_exists($filePath)){
       // echo "ok";
        $this->{$fileType} = str_replace($this->basePath, "/", $filePath );
        
}else{
       // echo "ko";
        $this->{$fileType} = NULL;
}
}
    
    
    
    

}
