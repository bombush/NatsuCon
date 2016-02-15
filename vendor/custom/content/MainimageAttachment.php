<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Custom\Content;

/**
 * Description of MainimageAttachment
 *
 * @author Josef Hruby
 */
class MainimageAttachment {
    private $_row;
    const THUMB_SMALL = "1000x700";
  // const THUMB_LIST= "200x200";
    const IMAGE_DIR = 'images/uploaded/attachment/';
    const THUMB_DIR = 'thumbs/';
    public $smallFile;
    public $listFile;
    public $originalFile;
    
    
    public function setRow($row) {
        $this->_row = $row;
    }
    
    public function create(){
        $this->saveOriginal();
    }
    
    private function saveOriginal(){
        $this->_row->file->move(self::IMAGE_DIR.$this->_row->contentId."-".$this->_row->file->getSanitizedName());
        //exit;
        
    }
    
}
