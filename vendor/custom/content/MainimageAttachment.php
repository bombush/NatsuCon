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
}
