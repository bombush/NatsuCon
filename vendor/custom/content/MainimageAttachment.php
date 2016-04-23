<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Custom\Content;
use Nette\Utils\ArrayHash;

/**
 * Description of MainimageAttachment
 *
 * @author Josef Hruby
 */
class MainimageAttachment extends ImageAttachment {
    const THUMB_SMALL = "300x50";
  // const THUMB_LIST= "200x200";

    const FULL = '1200x200';

    const IMAGE_DIR = 'images/uploaded/attachment/';
    const THUMB_DIR = 'thumbs/';
    
    
    public function setRow($row) {
        $this->_row = $row;
    }
    
    public function create(){
        $finalFilename = $this->generateFinalFilename();
        $this->saveOriginal();
        $this->copyToThumbs(self::IMAGE_DIR.$this->_row->contentId."-".$this->_row->file->getSanitizedName());
        return $finalFilename;
        
    }
    
    private function saveOriginal(){
        $this->_row->file->move(self::IMAGE_DIR.$this->_row->contentId."-".$this->_row->file->getSanitizedName());
  
    }
    
    private function copyToThumbs($filename){
        copy(self::IMAGE_DIR.$this->_row->contentId."-".$this->_row->file->getSanitizedName(), self::IMAGE_DIR.self::THUMB_DIR.$this->_row->contentId."-".$this->_row->file->getSanitizedName());
    }
    

}
